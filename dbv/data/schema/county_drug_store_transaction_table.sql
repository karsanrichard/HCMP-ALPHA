CREATE TABLE `county_drug_store_transaction_table` (
  `id` int(11) NOT NULL,
  `facility_code` int(11) NOT NULL,
  `commodity_id` int(11) NOT NULL,
  `opening_balance` int(11) NOT NULL DEFAULT '0',
  `total_receipts` int(11) NOT NULL DEFAULT '0',
  `total_issues` int(11) NOT NULL DEFAULT '0',
  `closing_stock` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `days_out_of_stock` int(11) NOT NULL DEFAULT '0',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `adjustmentpve` int(11) NOT NULL DEFAULT '0',
  `adjustmentnve` int(11) NOT NULL DEFAULT '0',
  `losses` int(11) NOT NULL DEFAULT '0',
  `quantity_ordered` int(11) NOT NULL DEFAULT '0',
  `comment` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1