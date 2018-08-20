<?php

class Xhgui_Saver_Mongo implements Xhgui_Saver_Interface
{
    /**
     * @var MongoCollection
     */
    private $_collection;

    /**
     * @var MongoId lastProfilingId
     */
    private static $lastProfilingId;

    public function __construct(MongoCollection $collection)
    {
        $this->_collection = $collection;
    }

    /**
     * @param array $data
     *
     * @return array|bool
     * @throws MongoCursorException
     * @throws MongoCursorTimeoutException
     * @throws MongoException
     * @throws Exception
     */
    public function save(array $data)
    {
        if (!class_exists('MongoDate')) {
            throw new \Exception(get_called_class() . ' requires alcaeus/mongo-php-adapter to be installed');
        }

        if (!isset($data['_id'])) {
            $data['_id'] = self::getLastProfilingId();
        }

        if (isset($data['meta']['request_ts'])) {
            $data['meta']['request_ts'] = new MongoDate($data['meta']['request_ts']['sec']);
        }

        if (isset($data['meta']['request_ts_micro'])) {
            $data['meta']['request_ts_micro'] = new MongoDate(
                $data['meta']['request_ts_micro']['sec'],
                $data['meta']['request_ts_micro']['usec']
            );
        }


        return $this->_collection->insert($data, array('w' => 0));
    }

    /**
     * Return profiling ID
     * @return MongoId lastProfilingId
     */
    public static function getLastProfilingId() {
        if (!self::$lastProfilingId) {
            self::$lastProfilingId = new MongoId();
        }
        return self::$lastProfilingId;
    }
}
