<?php
/**
 * Copyright (C) 2022  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Krabo\IsotopePackagingSlipOrderStatusUpdateBundle\EventListener;

use Contao\System;
use Isotope\Model\ProductCollection\Order;
use Krabo\IsotopePackagingSlipBundle\Event\Events;
use Krabo\IsotopePackagingSlipBundle\Event\StatusChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PackageSlipStatusChangedListener implements EventSubscriberInterface {

  /**
   * Returns an array of event names this subscriber wants to listen to.
   *
   * The array keys are event names and the value can be:
   *
   *  * The method name to call (priority defaults to 0)
   *  * An array composed of the method name to call and the priority
   *  * An array of arrays composed of the method names to call and respective
   *    priorities, or 0 if unset
   *
   * For instance:
   *
   *  * ['eventName' => 'methodName']
   *  * ['eventName' => ['methodName', $priority]]
   *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
   *
   * The code must not depend on runtime state as it will only be called at
   * compile time. All logic depending on runtime state must be put into the
   * individual methods handling the events.
   *
   * @return array<string, mixed> The event names to listen to
   */
  public static function getSubscribedEvents() {
    return [
      Events::STATUS_CHANGED_EVENT => 'onStatusChanged',
    ];
  }

  /**
   *
   * @param \Krabo\IsotopePackagingSlipBundle\Event\StatusChangedEvent $event
   *
   * @return void
   */
  public function onStatusChanged(StatusChangedEvent $event) {
    foreach ($event->getPackagingSlip()->getOrders() as $order) {
      if ($order->isPaid()) {
        $this->updatePaidStatus($order, $event->getNewStatus());
      } else {
        $this->updateUnpaidStatus($order, $event->getNewStatus());
      }
    }
  }

  /**
   * @param \Isotope\Model\ProductCollection\Order $order
   * @param $newPackagingSlipStatus
   *
   * @return void
   */
  protected function updatePaidStatus(Order $order, $newPackagingSlipStatus) {
    if (!$order->isPaid()) {
      return;
    }
    $configs = System::getContainer()->getParameter('krabo.isotope-packaging-slip-order-status_update.config');
    foreach($configs as $config) {
      if ($config['order_is_paid'] && $config['packaging_slip_status'] == $newPackagingSlipStatus) {
        $order->updateOrderStatus($config['order_status_id']);
        break;
      }
    }
  }

  /**
   * @param \Isotope\Model\ProductCollection\Order $order
   * @param $newPackagingSlipStatus
   *
   * @return void
   */
  protected function updateUnpaidStatus(Order $order, $newPackagingSlipStatus) {
    if ($order->isPaid()) {
      return;
    }
    $configs = System::getContainer()->getParameter('krabo.isotope-packaging-slip-order-status_update.config');
    foreach($configs as $config) {
      if (!$config['order_is_paid'] && $config['packaging_slip_status'] == $newPackagingSlipStatus) {
        $order->updateOrderStatus($config['order_status_id']);
        $this->updatePaidStatus($order, $newPackagingSlipStatus);
        break;
      }
    }
  }


}