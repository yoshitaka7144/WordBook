<?php

/**
 * 共通で使用する関数
 * 
 * @author yoshitaka Nagai <yoshitaka7144@gmail.com>
 */

/**
 * エスケープ処理
 *
 * @param string $str 処理対象文字列
 * @return string エスケープ処理済み文字列
 */
function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
