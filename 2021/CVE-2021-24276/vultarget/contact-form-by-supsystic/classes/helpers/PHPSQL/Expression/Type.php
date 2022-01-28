<?php
/**
 * Type.php
 *
 * This file implements the constants for the expression types of 
 * the output of the PHPSQLParser.
 *
 * Copyright (c) 2010-2012, Justin Swanhart
 * with contributions by André Rothe <arothe@phosco.info, phosco@gmx.de>
 * with contributions by Dan Vande More <bigdan@gmail.com>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 */

namespace PHPSQL\Expression;

class Type {

    const USER_VARIABLE = "user_variable";
    const SESSION_VARIABLE = "session_variable";
    const GLOBAL_VARIABLE = "global_variable";
    const LOCAL_VARIABLE = "local_variable";

    const COLREF = "colref";
    const RESERVED = "reserved";
    const CONSTANT = "const";

    const AGGREGATE_FUNCTION = "aggregate_function";
    const SIMPLE_FUNCTION = "function";

    const EXPRESSION = "expression";
    const BRACKET_EXPRESSION = "bracket_expression";
    const TABLE_EXPRESSION = "table_expression";

    const SUBQUERY = "subquery";
    const IN_LIST = "in-list";
    const OPERATOR = "operator";
    const SIGN = "sign";
    const RECORD = "record";
    
    const MATCH_ARGUMENTS = "match-arguments";
    const MATCH_MODE = "match-mode";
    
    const ALIAS = "alias";
    const POSITION = "pos";
    
    const TEMPORARY_TABLE = "temporary_table";
    const TABLE = "table";
    const VIEW = "view";
    const DATABASE = "database";
    const SCHEMA = "schema";
}
