<?php
/**
 * Token.php
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

class Token {

    private $subTree;
    private $expression;
    private $key;
    private $token;
    private $tokenType;
    private $trim;
    private $upper;

    public function __construct($key = "", $token = "") {
        $this->subTree = false;
        $this->expression = "";
        $this->key = $key;
        $this->token = $token;
        $this->tokenType = false;
        $this->trim = trim($token);
        $this->upper = strtoupper($this->trim);
    }

    # TODO: we could replace it with a constructor new \PHPSQL\Expression\Token(this, "*")
    public function addToken($string) {
        $this->token .= $string;
    }

    public function isEnclosedWithinParenthesis() {
        return ($this->upper[0] === '(' && substr($this->upper, -1) === ')');
    }

    public function setSubTree($tree) {
        $this->subTree = $tree;
    }

    public function getSubTree() {
        return $this->subTree;
    }

    public function getUpper($idx = false) {
        return $idx !== false ? $this->upper[$idx] : $this->upper;
    }

    public function getTrim($idx = false) {
        return $idx !== false ? $this->trim[$idx] : $this->trim;
    }

    public function getToken($idx = false) {
        return $idx !== false ? $this->token[$idx] : $this->token;
    }

    public function setTokenType($type) {
        $this->tokenType = $type;
    }

    public function endsWith($needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        $start = $length * -1;
        return (substr($this->token, $start) === $needle);
    }

    public function isWhitespaceToken() {
        return ($this->trim === "");
    }

    public function isCommaToken() {
        return ($this->trim === ",");
    }

    public function isVariableToken() {
        return $this->upper[0] === '@';
    }

    public function isSubQueryToken() {
        return preg_match("/^\\(\\s*SELECT/i", $this->trim);
    }

    public function isExpression() {
        return $this->tokenType === \PHPSQL\Expression\Type::EXPRESSION;
    }

    public function isBracketExpression() {
        return $this->tokenType === \PHPSQL\Expression\Type::BRACKET_EXPRESSION;
    }

    public function isOperator() {
        return $this->tokenType === \PHPSQL\Expression\Type::OPERATOR;
    }

    public function isInList() {
        return $this->tokenType === \PHPSQL\Expression\Type::IN_LIST;
    }

    public function isFunction() {
        return $this->tokenType === \PHPSQL\Expression\Type::SIMPLE_FUNCTION;
    }

    public function isUnspecified() {
        return ($this->tokenType === false);
    }

    public function isAggregateFunction() {
        return $this->tokenType === \PHPSQL\Expression\Type::AGGREGATE_FUNCTION;
    }

    public function isColumnReference() {
        return $this->tokenType === \PHPSQL\Expression\Type::COLREF;
    }

    public function isConstant() {
        return $this->tokenType === \PHPSQL\Expression\Type::CONSTANT;
    }

    public function isSign() {
        return $this->tokenType === \PHPSQL\Expression\Type::SIGN;
    }

    public function isSubQuery() {
        return $this->tokenType === \PHPSQL\Expression\Type::SUBQUERY;
    }

    public function toArray() {
        return array('expr_type' => $this->tokenType, 'base_expr' => $this->token, 'sub_tree' => $this->subTree);
    }
}
