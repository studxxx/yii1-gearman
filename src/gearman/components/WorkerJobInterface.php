<?php

interface WorkerJobInterface
{
    /** @param $job */
    public function perform($job);
}
