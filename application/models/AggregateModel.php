<?php

abstract class AggregateModel extends CI_Model
{
    protected $group;
    protected $groupRound;
    protected $dataAge = 0; //days

    public function processData($data)
    {
        $convertedData = $this->convertData($data);
        $dates = array_keys($convertedData);
        $timeStart  = min($dates);
        $timeFinish = max($dates);

        $dataSaved  = $this->getDataSaved($timeStart, $timeFinish);
        $mergedData = $this->mergeDataToSave($convertedData, $dataSaved);
        $this->saveData($mergedData);
    }

    protected function convertData($data)
    {
        $res = array();
        foreach ($data as $timeOpen => $toolItem)
        {
            $timeOpen -= $timeOpen % $this->groupRound;
            if (! isset($res[$timeOpen])) {
                $res[$timeOpen] = array();
            }
            foreach ($toolItem as $tool => $item)
            {
                unset($item['max_id']);
                if (! isset($res[$timeOpen][$tool]))
                {
                    $newItem = $item;
                    $newItem['time_at'] = date('Y-m-d H:i:s', $timeOpen);
                }
                else
                {
                    $newItem = $res[$timeOpen][$tool];
                    $newItem['close'] = $item['close'];
                    $newItem['high']  = max($newItem['high'], $item['high']);
                    $newItem['low']   = min($newItem['low'],  $item['low']);
                }
                $res[$timeOpen][$tool] = $newItem;
            }
        }
        return $res;
    }

    protected function getDataSaved($start, $finish)
    {
        $this->db->select('id, tool, time_at, open, close, high, low')
            ->from('aggregate_' . $this->group)
            ->where('time_at >=', date('Y-m-d H:i:s', $start))
            ->where('time_at <=', date('Y-m-d H:i:s', $finish))
            ->order_by('id', 'asc');

        $res = array();
        foreach ($this->db->get()->result() as $row)
        {
            $timeOpen = strtotime($row->time_at);
            if (! isset($res[$timeOpen])) {
                $res[$timeOpen] = array();
            }
            $res[$timeOpen][$row->tool] = array(
                'id'      => $row->id,
                'tool'    => $row->tool,
                'time_at' => $row->time_at,
                'open'    => (float) $row->open,
                'close'   => (float) $row->close,
                'high'    => (float) $row->high,
                'low'     => (float) $row->low,
            );
        }
        return $res;
    }

    protected function mergeDataToSave($dataNew, $dataSaved)
    {
        $keys = array_unique(array_merge(array_keys($dataNew), array_keys($dataSaved)));
        sort($keys);

        $res = array();
        foreach ($keys as $key)
        {
            if (! isset($res[$key])) {
                $res[$key] = array();
            }

            if (! isset($dataSaved[$key]))
            {
                // insert
                $res[$key] = $dataNew[$key];
            }
            elseif (! isset($dataNew[$key]))
            {
                // no update dataSaved
            }
            else
            {
                $tools = array_unique(array_merge(array_keys($dataNew[$key]), array_keys($dataSaved[$key])));
                foreach ($tools as $tool)
                {
                    if (! isset($dataSaved[$key][$tool]))
                    {
                        $res[$key][$tool] = $dataNew[$key][$tool];
                    }
                    elseif (! isset($dataNew[$key][$tool]))
                    {
                        // no update dataSaved
                    }
                    else
                    {
                        $new   = $dataNew[$key][$tool];
                        $saved = $dataSaved[$key][$tool];
                        if (   $new['close'] != $saved['close']
                            || $new['low']   != $saved['low']
                            || $new['high']  != $saved['high'])
                        {
                            $saved['close'] = $new['close'];
                            $saved['high']  = max($saved['high'], $new['high']);
                            $saved['low']   = min($saved['low'],  $new['low']);
                            $res[$key][$tool] = $saved;
                        }
                        // else no update dataSaved
                    }
                }
            }
        }
        return $res;
    }

    protected function saveData($finalData)
    {
        foreach ($finalData as $timeOpen => $toolData)
        {
            foreach ($toolData as $tool => $data)
            {
                if (isset($data['id']))
                {
                    $this->db->where('id', $data['id']);
                    $update = array(
                        'high'  => $data['high'],
                        'low'   => $data['low'],
                        'close' => $data['close'],
                    );
                    $this->db->update('aggregate_' . $this->group, $update);
                }
                else
                {
                    $this->db->insert('aggregate_' . $this->group, $data);
                }
            }
        }
    }

    public function dropOldData()
    {
        if ($this->dataAge > 0)
        {
            $this->db->where('time_at <', date('Y-m-d H:i:s', time() - 86400 * $this->dataAge));
            $this->db->delete('aggregate_' . $this->group);
        }
    }

    // ???
    protected function getTimeClose()
    {
        return time() - time() % $this->groupRound;
    }
}

class AggregateKindException extends Exception
{
}
