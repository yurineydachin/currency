<?php
require_once dirname(__FILE__) . '/AggregateModel.php';

class AggregateH1 extends AggregateModel
{
    protected $group      = 'h1';
    protected $groupRound = 3600;
    protected $dataAge    = 10; //days
}
