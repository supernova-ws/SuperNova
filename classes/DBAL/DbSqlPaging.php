<?php
/**
 * Created by Gorlum 25.11.2017 18:49
 */

namespace DBAL;

/**
 * Class DbSqlPaging
 *
 * Another implementation for DbAbstractResultIterator
 *
 * Fetches data from string SQL-query and pages through it
 *
 * @package DBAL
 */
class DbSqlPaging {
  protected $db = null;
  protected $sqlQuery;
  protected $pageSize = PAGING_PAGE_SIZE_DEFAULT;
  protected $currentPage = 1;
  private ?PDORowIterator $iterator = null;

  private $totalRecords = 0;

  /**
   * DbSqlPaging constructor.
   *
   * @param string        $sqlQuery
   * @param int           $pageSize
   * @param int           $currentPage
   * @param db_mysql|null $db
   */
  public function __construct($sqlQuery, $pageSize = PAGING_PAGE_SIZE_DEFAULT, $currentPage = 1, $db = null) {
    $this->sqlQuery = $sqlQuery;
    $this->pageSize = max(intval($pageSize), PAGING_PAGE_SIZE_MINIMUM);
    $this->currentPage = max(intval($currentPage), 1);

    $this->db = isset($db) ? $db : \SN::$gc->db;

    $this->totalRecords = $this->db->countQueryRecords($sqlQuery);

    $this->query();
  }

  public function getIterator(): \Iterator {
    return $this->iterator;
  }

  /**
   * @inheritdoc
   */
  public function valid() {
    return $this->iterator->valid() && $this->iterator->key() < $this->getRecordsOnCurrentPage();
  }

  /**
   * @inheritdoc
   */
  public function count() {
    return $this->iterator instanceof PDORowIterator ? $this->getRecordsOnCurrentPage() : 0;
  }

  /**
   * @inheritdoc
   */
  protected function seekToFirst() {
    if ($this->iterator instanceof PDORowIterator) {
      $this->iterator->data_seek($this->getPageZeroRecordNum());
    }
  }

  /**
   * Get current page number
   *
   * @return int
   */
  public function getCurrentPageNumber() {
    return $this->currentPage;
  }

  /**
   * Get total number of records in query
   *
   * @return int
   */
  public function getTotalRecords() {
    return $this->totalRecords;
  }

  /**
   * Calculates total page numbers
   *
   * @return float|int
   */
  public function getTotalPages() {
    return ceil($this->getTotalRecords() / $this->pageSize);
  }

  /**
   * Calculates record count on current page
   *
   * @return int
   */
  public function getRecordsOnCurrentPage() {
    return min($this->pageSize, $this->getTotalRecords() - $this->getPageZeroRecordNum());
  }

  /**
   * Calculates record index from which current page starts
   *
   * @return int
   */
  protected function getPageZeroRecordNum() {
    return ($this->currentPage - 1) * $this->pageSize;
  }

  /**
   * Queries DB for data and makes all necessary preparations
   */
  protected function query() {
    // Checking if page number within allowed ranges
    if (
      // Is it not a first page?
      $this->currentPage != 1
      &&
      // Is current page beyond limits?
      $this->currentPage > $this->getTotalPages()
    ) {
      // Correcting page number to first
      $this->currentPage = max(1, $this->getTotalPages());
    }

    // Performing query
    $this->iterator = $this->db->selectIterator($this->sqlQuery . ' LIMIT ' . $this->pageSize . ' OFFSET ' . ($this->currentPage - 1) * $this->pageSize);

    $this->iterator->rewind();
  }

}
