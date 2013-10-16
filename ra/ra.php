<?php

/*
  Namespace: ra
  This namespace implements a more natural language for expressing
  relational algebra in PHP. It is more user-friendly to use in application
  code than manipulating SQL string directly. It is also easier to
  understand and read.

  The implementation only supports MySQL as backend.
*/
namespace ra;

class Relation {
}

abstract class Expression {
  private $alias;
  public function __construct() {
    $this->alias = "t".mt_rand();
  }
  public abstract function toSQL(mysqli $dbh);
  public function toExecutableSQL(mysqli $dbh) {
    return $this->toSQL($dbh);
  }
  public function getAlias() {
    return $alias;
  }
}
class SelectStatement extends Expression {
  private $select_exprs = array();
  private $table_refs = array();
  private $where_predicate = null;
  private $group = array();
  private $having = array();
  private $order_exprs = array();
  private $limit = null;
  private $skip = null;

  public function toSQL(mysqli $dbh) {
    $sql = "SELECT " . $this->alias($this->select_exprs, $dbh);
    if (count($table_refs) > 0) {
    }
  }
  public function alias(array $elems, mysqli $dbh) {
    $arr = array();
    foreach ($elems as $k => $v) {
      if ($v instanceof Expression) {
        $v = $v->toSQL($dbh);
      }
      $str = (string)$v;
      if (!is_numeric($k)) {
        $str .= " AS $k";
      }
      $arr[] = $str;
    }
    return implode(',', $arr);
  }
}
class ERelation extends Expression {
  public $relation;
  public function __construct(Relation $r) { parent::__construct(); $this->relation = $; }
  public function toExecutableSQL(mysqli $dbh) {
    return "SELECT * FROM ".$this->toSQL($dbh);
  }
  public function toSQL(mysqli $dbh) {
    return $this->relation; //Just the relation name
  }
}
class EProjection extends Expression {
  public $attributes;
  public $expression;
  public function __construct(array $attr, Expression $expr) {
    parent::__construct();
    $this->attributes = $attr;
    $this->expression = $expr;
  }
  public function toSQL(mysqli $dbh) {
    $fieldlist = implode(',', $this->attributes);
    return "SELECT ".$fieldlist." FROM (".$this->expression->toSQL($dbh).")" . " AS " . $this->expression->getAlias();
  }
}
class ESelection extends Expression {
  public $predicate;
  public $expression;
  public function __construct(Predicate $p, Expression $expr) {
    $this->predicate = $p;
    $this->expression = $expr;
  }
  public function toSQL(mysqli $dbh) {
    return "SELECT * FROM (".$this->expression->toSQL($dbh).") AS ".$this->expression->getAlias()." WHERE ".$this->predicate->toSQL($dbh);
  }
}
class EUnion extends Expression {
  public $p;
  public $q;
  public function __construct(Expression $p, Expression $q) {
    $this->p = $p;
    $this->q = $q;
  }
  public function toSQL(mysqli $dbh) {
    return $this->p->toExecutableSQL($dbh)." UNION ".$this->q->toExecutableSQL($dbh);
  }
}
class EJoin extends Expression {
  public $predicate;
  public $p;
  public $q;
  public function __construct(Predicate $pred, Expression $p, Expression $q) {
    $this->predicate = $pred;
    $this->p = $p;
    $this->q = $q;
  }
  public function toSQL(mysqli $dbh) {
    
  }
}

/** Basic RA **/
function relation(Relation $r) { return new ERelation($r); }

function union(Expression $p, Expression $q) { return new EUnion($p, $q); }

// Difference, intersect, and rename are not supported MySQL operations.

function project(array $attr, Expression $expr) { return new EProject($attr, $expr); }

function select(Predicate $pred, Expression $expr) { return new ESelection($pred, $expr); }

function product(Expression $p, Expression $q) {}

function join(Expression $p, Expression $q) {}

function theta_join(Predicate $rules, Expression $p, Expression $q) {}

/** Basic predicate construction **/
abstract class Predicate {
  public abstract function toSQL(mysqli $dbh);
}
class PCond extends Predicate {
  const FLAG_LITERAL = 0x01;

  public $op;
  public $field;
  public $value;
  public $flags;
  public function __construct($op, $field, $value, $flags) {
    $this->op = $op;
    $this->field = $field;
    $this->value = $value;
    $this->flags = $flags;
  }
  public function flag($flag) {
    return new PCond($this->op, $this->field, $this->value, $this->flags | $flag);
  }
  public function hasFlag($flag) {
    return $flag === $flag & $this->flags;
  }
  public function toSQL(mysqli $dbh) {
    $val = ($this->value instanceof Expression ? $this->value->toSQL($dbh) : $this->value);
    $val = $this->hasFlag(self::FLAG_LITERAL) ? $val : mysqli_real_escape_string($dbh, $val);
    return $this->field . " " . $this->op . " " . $val;
  }
}
class POr extends Predicate {
  public $ps;
  public function __construct(array $ps) { $this->ps = $ps; }
  public function toSQL(mysqli $dbh) {
    return implode(' OR ', array_map(function(PCond $p) use(&$dbh) {
      return $p->toSQL($dbh);
    }, $ps));
  }
}
class PAnd extends Predicate {
  public $ps;
  public function __construct(array $ps) { $this->ps = $ps; }
  public function toSQL(mysqli $dbh) {
    return implode(' AND ', array_map(function(PCond $p) use(&$dbh) {
      return $p->toSQL($dbh);
    }, $ps));
  }
}
function wedge() { return new PAnd(func_get_args()); }
function vee() { return new POr(func_get_args()); }
function eq($field, $value) {
  return cond('=', $field, $value);
}
function neq($field, $value) {
  return cond('!=', $field, $value);
}
function geq($field, $value) {
  return cond('>=', $field, $value);
}
function leq($field, $value) {
  return cond('<=', $field, $value);
}
function lt($field, $value) {
  return cond('<', $field, $value);
}
function gt($field, $value, $options=null) {
  return cond('>', $field, $value);
}
function eqns($field, $value) {
  return cond('<=>', $field, $value);
}
function neqns($field, $value) {
  return cond('NOT <=>', $field, $value);
}
function like($field, $value) {
  return cond('LIKE', $field, $value);
}
function nlike($field, $value) {
  return cond('NOT LIKE', $field, $value);
}
function between($field, $lower, $upper) {
  return cond('BETWEEN', $field, array($lower, $upper));
}
function nbetween($field, $lower, $upper) {
  return cond('NOT BETWEEN', $field, array($lower, $upper));
}
function in($field, $value) {
  return cond('IN', $field, $value);
}
function nin($field, $value) {
  return cond('NOT IN', $field, $value);
}
function isnull($field) {
  return cond('IS', $field, null);
}
function nisnull($field) {
  return cond('IS NOT', $field, null);
}
function exists(Expression $e) {
  return cond('EXISTS', null, $e);
}
function nexists(Expression $e) {
  return cond('NOT EXISTS', null, $e);
}
function cond($op, $field, $value, $flags = 0) {
  return new PCond($op, $field, $value, $flags);
}
function literal(PCond $c) {
  return $c->flag(PCond::FLAG_LITERAL);
}



?>
