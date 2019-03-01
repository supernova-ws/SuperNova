<?php
/**
 * Created by Gorlum 01.03.2019 9:06
 */

namespace DBAL;

class DbIndexField {
  public $Column_name; // Column_name - The column name. See also the description for the Expression column.
  public $Seq_in_index; // The column sequence number in the index, starting with 1.
  public $Collation; // How the column is sorted in the index. This can have values A (ascending), D (descending), or NULL (not sorted).
  public $Cardinality; // An estimate of the number of unique values in the index. To update this number, run ANALYZE TABLE or (for MyISAM tables) myisamchk -a.
  public $Sub_part; // The index prefix. That is, the number of indexed characters if the column is only partly indexed, NULL if the entire column is indexed.
  public $Null; // Contains YES if the column may contain NULL values and '' if not.
  public $Expression; // MySQL 8.0.13 and higher supports functional key parts (see Functional Key Parts), which affects both the Column_name and Expression columns:

  public function __construct($indexField = []) {
    is_array($indexField) && !empty($indexField) ? $this->fromMySqlDescription($indexField) : false;
  }

  /**
   * @param array $indexField
   */
  public function fromMySqlDescription($indexField) {
    $this->Seq_in_index = isset($indexField['Seq_in_index']) ? $indexField['Seq_in_index'] : null;
    $this->Column_name  = isset($indexField['Column_name']) ? $indexField['Column_name'] : null;
    $this->Collation    = isset($indexField['Collation']) ? $indexField['Collation'] : null;
    $this->Cardinality  = isset($indexField['Cardinality']) ? $indexField['Cardinality'] : null;
    $this->Sub_part     = isset($indexField['Sub_part']) ? $indexField['Sub_part'] : null;
    $this->Null         = isset($indexField['Null']) ? $indexField['Null'] : null;
    $this->Expression   = isset($indexField['Expression']) ? $indexField['Expression'] : null;

    return $this;
  }

}
