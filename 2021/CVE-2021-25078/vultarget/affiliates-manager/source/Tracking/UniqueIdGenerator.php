<?php

/**
 * @author John Hargrove
 * 
 * Date: Jun 6, 2010
 * Time: 7:55:09 PM
 */
class WPAM_Tracking_UniqueIdGenerator {

    public function generateId() {
        return $this->generateIdInternal(true);
    }

    public function generateIdString() {
        return $this->generateIdInternal(false);
    }

    private function generateIdInternal($raw) {
        return sha1(microtime() . mt_rand() . uniqid(mt_rand(), true), $raw);
    }

    private function generateIdInternalNew($input) {
        return uniqid();
    }

}
