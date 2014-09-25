<?php
require_once dirname(__FILE__) . '/AggregateModel.php';

class AggregateM1 extends AggregateModel
{
    protected $group      = 'm1';
    protected $groupRound = 60;
    protected $dataAge    = 1; //day
}
