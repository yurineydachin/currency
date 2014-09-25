<?php
require_once dirname(__FILE__) . '/AggregateModel.php';

class AggregateD1 extends AggregateModel
{
    protected $group      = 'd1';
    protected $groupRound = 86400;
}
