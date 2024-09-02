<?php
/*
 * This script displays a table of orders:
 * 1. Includes data from 'orders-data.php' to fetch order details.
 * 2. Displays a table with columns for order ID, date, products, status, customer name, address, and cost.
 * 3. Shows a warning message if no orders are found.
 */
include_once 'orders-data.php'; ?>
  <div class="col-md-12">
    <?php
    if(isset($orders) && count($orders)>0)
    {
    ?>
    <table class="table table-hover products-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Date</th>
          <th>Products</th>
          <th>Status</th>
          <th>Name</th>
          <th>Address</th>
          <th>Cost</th>
        </tr>
      </thead>
      <tbody>
        <?php
          foreach ($orders as $order) {
        ?>
          <tr>
            <td><?php echo $order->od_id; ?></td>
            <td><?php echo $order->od_date; ?></td>
            <td><?php echo $order->products; ?></td>
            <td><?php echo $order->od_status; ?></td>
            <td><?php echo $order->od_name; ?></td>
            <td><?php echo $order->od_address . '<br>' . $order->od_city . ' ' . $order->od_postal_code; ?></td>
            <td class="text-center">$ <?php echo $order->od_cost ?></td>
          </tr>
        <?php
          }
        ?>
      </tbody>
    </table>
    <?php
    }
    else { ?>
      <div class="alert alert-warning"><strong></strong> Didn't find any orders, please add some.</div>
    <?php
    }
    ?>
  </div><!-- /col-md-10 -->