<?php
class FlowModel extends CI_Model
{
    const DEFAULT_AGGREGATE_FIELD = 'bid';

    private $typeToSave = array(
        'S' => 'tool',
        'T' => 'added_at',
        'B' => 'bid',
        'A' => 'ask',
    );

    function addRow($data)
    {
        $save = array();
        foreach ($this->typeToSave as $key => $field)
        {
            if (isset($data[$key]))
            {
                $save[$field] = $data[$key];
            }
            else
            {
                throw new FlowAddRowException('Field %s is required for inserting flow');
            }
        }

        $save['added_at'] = date('Y-m-d ') . $save['added_at'];
        $save['tool']     = strtoupper($save['tool']);
        return $this->db->insert('flow', $save);
    }

    public function aggregate($group = 'm1', array $params = array())
    {
        $groupConds = array(
            'm1' => array('t_m1'),
            'm5' => array('t_m5'),
            'h1' => array('t_h1'),
            'd1' => array('t_d1'),
            'w1' => array('y1', 'w1'),
        );
        if (isset($groupConds[$group]))
        {
            $groupCond = $groupConds[$group];
        }
        else
        {
            throw new AggregateKindException('Unknow kind aggregate: ' . $group);
        }

        $groupBy = array('tool');
        if (in_array('ym', $groupCond)) {
            $groupBy[] = 'EXTRACT(YEAR_MONTH from added_at)';
        }
        if (in_array('y1', $groupCond)) {
            $groupBy[] = 'EXTRACT(YEAR from added_at)';
        }
        if (in_array('w1', $groupCond)) {
            $groupBy[] = 'EXTRACT(WEEK from added_at)';
        }
        if (in_array('t_d1', $groupCond)) {
            $groupBy[] = 'FLOOR(UNIX_TIMESTAMP(added_at)/ 86400)';
        }
        if (in_array('t_h1', $groupCond)) {
            $groupBy[] = 'FLOOR(UNIX_TIMESTAMP(added_at)/ 3600)';
        }
        if (in_array('t_m5', $groupCond)) {
            $groupBy[] = 'FLOOR(UNIX_TIMESTAMP(added_at)/ 300)';
        }
        if (in_array('t_m1', $groupCond)) {
            $groupBy[] = 'FLOOR(UNIX_TIMESTAMP(added_at)/ 60)';
        }
        if ($groupBy) {
            $groupBy = ' GROUP BY ' . implode(', ', $groupBy);
        } else {
            $groupBy = '';
        }

        $where = array();
        if (isset($params['start'])) {
            $where[] = " added_at >= " . $this->db->escape(date('Y-m-d H:i:s', $params['start'])) . "";
        }
        if (isset($params['finish'])) {
            $where[] = " added_at <= " . $this->db->escape(date('Y-m-d H:i:s', $params['finish'])) . "";
        }
        if (isset($params['tool'])) {
            $where[] = " tool = " . $this->db->escape($params['tool']) . "";
        }
        if ($where) {
            $where = ' WHERE ' . implode(' AND ', $where);
        } else {
            $where = '';
        }

        $field = self::DEFAULT_AGGREGATE_FIELD;
        $sql = "select t.tool, t.max_id, t.high, t.low, round(b1.$field, 5) as open, round(b2.$field, 5) as close, b1.added_at as time_open
from
(SELECT 
  tool,
  round(max($field), 5) AS high,
  round(min($field), 5) AS low,
  min(id) as min_id,
  max(id) as max_id
FROM curr_flow
$where
$groupBy) t
inner join curr_flow b1 on b1.id = t.min_id
inner join curr_flow b2 on b2.id = t.max_id
order by b1.added_at asc";

        $data = $this->db->query($sql)->result_array();
        $res = array();
        foreach ($data as $row)
        {
            $timeOpen = strtotime($row['time_open']);
            if (! isset($res[$timeOpen]))
            {
                $res[$timeOpen] = array();
            }
            unset($row['time_open']);
            $row['open']      = (float) $row['open'];
            $row['close']     = (float) $row['close'];
            $row['high']      = (float) $row['high'];
            $row['low']       = (float) $row['low'];
            $res[$timeOpen][$row['tool']] = $row;
        }
        return $res;
    }

    public function deleteBeforeId($id)
    {
        $this->db->where('id <=', $id);
        $this->db->delete('flow');
    }
}

class FlowAddRowException extends Exception
{
}
