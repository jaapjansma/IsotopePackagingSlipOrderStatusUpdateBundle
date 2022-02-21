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
    $configs = System::getContainer()->getParameter('krabo.isotope-packaging-slip-order-status_update.config');
    $statusUpdated = false;
    foreach($configs as $config) {
      if ($config['packaging_slip_status'] == $event->getNewStatus()) {
        foreach ($event->getPackagingSlip()->getOrders() as $order) {
          if ($config['order_is_paid'] && $order->isPaid()) {
            $order->updateOrderStatus($config['order_status_id']);
            $statusUpdated = true;
          } elseif (!$config['order_is_paid'] && !$order->isPaid()) {
            $order->updateOrderStatus($config['order_status_id']);
            $statusUpdated = true;
          }
        }
      }
      if ($statusUpdated) {
        // Stop at the first processed rule.
        break;
      }
    }
  }


}