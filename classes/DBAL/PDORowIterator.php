<?php
/** Created by Gorlum 26.12.2025 16:47 */

namespace DBAL;

use PDO;
use PDOStatement;

class PDORowIterator implements \Iterator, \Countable
{
  private PDOStatement $stmt;
  private array $rows = [];
  private int $position = 0;
  private bool $loaded = false;

  public function __construct(PDOStatement $stmt)
  {
    $this->stmt = $stmt;
    $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
  }

  /* ===== mysqli-подобные методы ===== */

  public function fetch_assoc(): ?array
  {
    $this->load();

    if (!isset($this->rows[$this->position])) {
      return null;
    }

    return $this->rows[$this->position++];
  }

  public function data_seek(int $offset): bool
  {
    $this->load();

    if ($offset < 0 || $offset >= count($this->rows)) {
      return false;
    }

    $this->position = $offset;
    return true;
  }

  public function num_rows(): int
  {
    return $this->count();
  }

  /* ===== Iterator ===== */

  public function current(): mixed
  {
    return $this->rows[$this->position];
  }

  public function key(): int
  {
    return $this->position;
  }

  public function next(): void
  {
    $this->position++;
  }

  public function rewind(): void
  {
    $this->load();
    $this->position = 0;
  }

  public function valid(): bool
  {
    return isset($this->rows[$this->position]);
  }

  /* ===== Countable ===== */

  public function count(): int
  {
    $this->load();
    return count($this->rows);
  }

  /* ===== internal ===== */

  private function load(): void
  {
    if (!$this->loaded) {
      $this->rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
      $this->loaded = true;
    }
  }

}
