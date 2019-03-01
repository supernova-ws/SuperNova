<?php
/**
 * Created by Gorlum 04.10.2017 8:55
 */

namespace DBAL;

/**
 * Class DbIndexDescription
 * @package DBAL
 *
 * Objects of this class contains MySql index description
 */
class DbIndexDescription {
  public $Key_name = ''; // Key_name - The name of the index. If the index is the primary key, the name is always PRIMARY.
  public $Non_unique = 1; // 0 if the index cannot contain duplicates, 1 if it can.

  public $Packed = null; // Indicates how the key is packed. NULL if it is not.
  public $Index_type = 'BTREE'; // The index method used (BTREE, FULLTEXT, HASH, RTREE).
  public $Comment = ''; // Information about the index not described in its own column, such as disabled if the index is disabled.
  public $Index_comment = ''; // Any comment provided for the index with a COMMENT attribute when the index was created.
  public $Visible; // Whether the index is visible to the optimizer. See Section 8.3.12, “Invisible Indexes”.

  /**
   * @var DbIndexField[]
   */
  public $fields = [];

  protected $sorted = true;

  /**
   * @param array $indexField
   *
   * @return $this
   */
  public function addField($indexField) {
    $this->Key_name      = isset($indexField['Key_name']) ? $indexField['Key_name'] : null;
    $this->Non_unique    = isset($indexField['Non_unique']) ? $indexField['Non_unique'] : null;
    $this->Packed        = isset($indexField['Packed']) ? $indexField['Packed'] : null;
    $this->Index_type    = isset($indexField['Index_type']) ? $indexField['Index_type'] : null;
    $this->Comment       = isset($indexField['Comment']) ? $indexField['Comment'] : null;
    $this->Index_comment = isset($indexField['Index_comment']) ? $indexField['Index_comment'] : null;
    $this->Visible       = isset($indexField['Visible']) ? $indexField['Visible'] : null;

    if (isset($indexField['Column_name']) || isset($indexField['Expression'])) {
      $fullName = $indexField['Column_name'] .
        (!empty($indexField['Expression']) ? '|' . $indexField['Expression'] : '');

      $this->fields[$fullName] = new DbIndexField($indexField);
      $this->sorted            = false;
    }

    return $this;
  }

  /**
   * @return string
   */
  public function signature() {
    $this->sort();


    return implode(',', array_keys($this->fields));
  }

  protected function sort() {
    if (!$this->sorted) {
      asort($this->fields, function (DbIndexField $a, DbIndexField $b) {
        return $a->Seq_in_index - $b->Seq_in_index;
      });
    }
  }

}
