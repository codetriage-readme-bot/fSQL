<?php

namespace FSQL\Statements;

use FSQL\Environment;

class Delete extends DataModifyStatement
{
    private $tableFullName;
    private $where;

    public function __construct(Environment $environment, array $fullName, $where)
    {
        parent::__construct($environment);
        $this->tableFullName = $fullName;
        $this->where = $where;
    }

    public function execute()
    {
        $this->affected = 0;

        $table = $this->environment->find_table($this->tableFullName);
        if(!$table)
            return false;

        $cursor = $table->getWriteCursor();
        if($this->where) {
            $where = "return " . $this->where .";";
            var_dump($where);
            for($cursor->rewind(); $cursor->valid(); ) {
                $entry = $cursor->current();
                if(eval($where)) {
                    $cursor->deleteRow();
                    $this->affected++;
                } else {
                    $cursor->next();
                }
            }
        } else {
            $c = 0;
            for($cursor->rewind();  $cursor->valid(); $cursor->deleteRow()) {
                ++$c;
            }
            $this->affected = $c;
        }

        $this->commit($table);

        return true;
    }
}
