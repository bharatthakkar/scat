<?
require 'scat.php';

head("transactions");

$type= $_REQUEST['type'];
if ($type) {
  $criteria= "type = '".$db->real_escape_string($type)."'";
} else {
  $criteria= '1=1';
}

?>
<form method="get" action="txn.php">
<select name="type">
 <option value="customer">Invoice
 <option value="vendor">Purchase Order
 <option value="internal">Internal
</select>
<input id="focus" type="text" name="number" value="">
<input type="submit" value="Look Up">
</form>
<br>
<?

$q= "SELECT
            txn.type AS meta,
            CONCAT(txn.id, '|', type, '|', txn.number) AS Number\$txn,
            txn.created AS Created\$date,
            CONCAT(txn.person, '|', IFNULL(person.company,''),
                   '|', IFNULL(person.name,''))
              AS Person\$person,
            SUM(ordered) AS Ordered,
            SUM(shipped) AS Shipped,
            SUM(allocated) AS Allocated
       FROM txn
       LEFT JOIN txn_line ON (txn.id = txn_line.txn)
       LEFT JOIN person ON (txn.person = person.id)
      WHERE $criteria
      GROUP BY txn.id
      ORDER BY created DESC
      LIMIT 200";

dump_table($db->query($q));
dump_query($q);
