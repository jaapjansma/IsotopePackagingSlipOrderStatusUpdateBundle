# Isotope Packaging Slip Order Status Update Bundle

Contao Isotope Packaging Slip Order Status Update Bundle

When an packaging slip is updated to a new status update the corresponding order to a new status.

## Requirements

* Contao > 4.9
* Isotope
* [Isotope Packaging Slip Bundle](https://packagist.org/packages/krabo/isotope-packaging-slip-bundle)

## Configuration options:

The following configuration can be adjusted to your needs and added to your config.yml file.

```yaml 
isotope_packaging_slip_order_status_update:
  # Define status regels.
  # De eerste gematched regel wordt verwerkt.
  # Het zoeken gebeurt op zoeken naar de packaging_slip_status
  #  - 1 = Prepare for shipping
  #  - 2 = Shipped
  #  - 3 = Ready for pickup
  #  - 4 = Delivered
  #  - 5 = Picked up
  # order_is_paid definieert of de bestelling betaald dient te zijn of niet
  # order_status is de id van de nieuwe bestel status
  status_config:
    - {packaging_slip_status: 2, order_is_paid: true, order_status_id: 7}

```

## Contributions

Contributions to this bundle are more than welcome. Please submit your contributions as a merge request.

## License

The extension is licensed under [AGPL-3.0](LICENSE.txt)