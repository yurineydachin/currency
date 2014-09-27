<?php

abstract class AggregateModel extends CI_Model
{
    const DEFAULT_LIMIT  = 50;
    const DEFAULT_OFFSET = 0;

    protected $group;
    protected $groupRound;
    protected $dataAge = 0; //days

    public function getActualityData($tool)
    {
        $params = array(
            'limit' => self::DEFAULT_LIMIT,
        );
        if ($tool) {
            $params['tool'] = $tool;
        }
        $dataSaved = $this->getDataSaved($params);
        $timeOpen = $dataSaved ? key($dataSaved) : null;

        $params = array();
        if ($timeOpen) {
            $params['start'] = $timeOpen;
            $params['start_h'] = date('Y-m-d H:i:s', $timeOpen);
        }
        $params['finish'] = time();
        if ($tool) {
            $params['tool'] = $tool;
        }

        $data = $this->FlowModel->aggregate('m1', $params);
        $convertedData = $this->convertData($data);

        $result = $this->mergeDataToSave($convertedData, $dataSaved, false);
        if (count($result) > self::DEFAULT_LIMIT)
        {
            return array_slice($result, (-1) * self::DEFAULT_LIMIT);
        }
        else
        {
            return $result;
        }
    }

    public function update($data)
    {
        $convertedData = $this->convertData($data);
        $dates = array_keys($convertedData);

        $params = array(
            'start'  => min($dates),
            'finish' => max($dates),
        );
        $dataSaved  = $this->getDataSaved($params);
        $mergedData = $this->mergeDataToSave($convertedData, $dataSaved, true);
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

    public function getDataSaved(array $params)
    {
        $this->db->select('id, tool, time_at, open, close, high, low')
            ->from('aggregate_' . $this->group)
            ->order_by('id', 'desc');

        if (isset($params['start'])) {
            $this->db->where('time_at >=', date('Y-m-d H:i:s', $params['start']));
        }
        if (isset($params['finish'])) {
            $this->db->where('time_at <=', date('Y-m-d H:i:s', $params['finish']));
        }
        if (isset($params['tool'])) {
            $this->db->where('tool', $params['tool']);
        }
        if (isset($params['limit']) || isset($params['offset']))
        {
            $params['limit']  = isset($params['limit'])  ? $params['limit']  : self::DEFAULT_LIMIT;
            $params['offset'] = isset($params['offset']) ? $params['offset'] : self::DEFAULT_OFFSET;
            $this->db->limit($params['limit'], $params['offset']);
        }

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
        return array_reverse($res, true); // order ASC - same flowModel->aggregate
    }

    protected function mergeDataToSave($dataNew, $dataSaved, $toUpdate = true)
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
                if ($toUpdate) {
                    // no update dataSaved
                } else {
                    $res[$key] = $dataSaved[$key];
                }
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
                        if ($toUpdate) {
                            // no update dataSaved
                        } else {
                            $res[$key][$tool] = $dataSaved[$key][$tool];
                        }
                    }
                    else
                    {
                        $new   = $dataNew[$key][$tool];
                        $saved = $dataSaved[$key][$tool];
                        $saved['close'] = $new['close'];
                        $saved['high']  = max($saved['high'], $new['high']);
                        $saved['low']   = min($saved['low'],  $new['low']);
                        $res[$key][$tool] = $saved;
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

class AggregateDateIntervalException extends Exception
{
}
