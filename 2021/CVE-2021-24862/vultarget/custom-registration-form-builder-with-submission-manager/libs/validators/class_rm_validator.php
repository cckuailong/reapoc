<?php
/* For custom validations which can not be included in PFBC */
interface RM_Validator{
    public function is_valid($value);
}