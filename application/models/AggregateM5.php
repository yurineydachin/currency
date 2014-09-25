<?php
require_once dirname(__FILE__) . '/AggregateModel.php';

class AggregateM5 extends AggregateModel
{
    protected $group      = 'm5';
    protected $groupRound = 300;
    protected $dataAge    = 3; //days
}
