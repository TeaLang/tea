<?php
/**
 * This file is part of the Tea programming language project
 * @copyright 	(c)2019 tealang.org
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Tea;

trait TeaStringTrait
{
	protected function read_plain_literal_string()
	{
		$expression = new PlainLiteralString($this->read_quoted_string(_SINGLE_QUOTE));
		$expression->pos = $this->pos;
		return $expression;
	}

	protected function read_escaped_literal_string()
	{
		$expression = new EscapedLiteralString($this->read_quoted_string(_DOUBLE_QUOTE));
		$expression->pos = $this->pos;
		return $expression;
	}

	protected function read_quoted_string(string $quote_mark)
	{
		$string = '';
		while (($token = $this->scan_string_component()) !== null) {
			if ($token === $quote_mark) {
				return $string;
			}

			$string .= $token;
			if ($token === _BACK_SLASH) {
				$string .= $this->scan_string_component(); // ignore the escaped char
			}
		}

		throw $this->new_parse_error("Missed the close quote mark ($quote_mark).");
	}

	protected function read_single_quoted_expression()
	{
		$items = $this->read_quoted_items(_SINGLE_QUOTE);

		if (empty($items)) {
			$expression = new PlainLiteralString(_NOTHING);
		}
		elseif (!isset($items[1]) && is_string($items[0])) {
			$expression = new PlainLiteralString($items[0]);
		}
		else {
			$expression = new PlainInterpolatedString($items);
		}

		$expression->pos = $this->pos;
		return $expression;
	}

	protected function read_double_quoted_expression()
	{
		$items = $this->read_quoted_items(_DOUBLE_QUOTE);

		if (empty($items)) {
			$expression = new PlainLiteralString(_NOTHING);
		}
		elseif (count($items) === 1 && is_string($items[0])) {
			$expression = new EscapedLiteralString($items[0]);
		}
		else {
			$expression = new EscapedInterpolatedString($items);
		}

		$expression->pos = $this->pos;
		return $expression;
	}

	protected function read_quoted_items(string $quote_mark): array
	{
		$items = [];
		$string = '';
		while (($token = $this->scan_string_component()) !== null) {
			if ($token === $quote_mark) {
				if ($string !== '') {
					$items[] = $string;
				}

				return $items;
			}
			elseif ($token === _DOLLAR_BRACE_OPEN) {
				$expression = $this->scan_normal_interpolation();
				if ($expression === null) {
					$string .= $token;
					continue;
				}

				static::collect_and_reset_temp($items, $string, $expression);
				continue;
			}
			// elseif ($token === _SHARP && $this->skip_token(_BLOCK_BEGIN)) {
			// 	$expression = $this->read_html_escaping_interpolation();
			// 	if ($expression) {
			// 		static::collect_and_reset_temp($items, $string, $expression);
			// 	}
			// 	continue;
			// }
			elseif ($token === _BACK_SLASH) {
				$string .= $token . $this->scan_string_component();
				continue;
			}

			$string .= $token;
		}

		throw $this->new_parse_error("Missed the quote close mark ($quote_mark).");
	}

	protected function scan_normal_interpolation(): ?BaseExpression
	{
		// if ($this->get_token() === _BLOCK_BEGIN) {
		// 	// ${ ... } style
		// 	$this->scan_token(); // skip {

			$expr = $this->read_expression();
			if ($expr === null) {
				throw $this->new_parse_error("Required an expression in \${}.");
			}

			$this->expect_block_end();
		// }
		// else {
		// 	$expr = $this->read_dollar_identifier();
		// }

		$expr = new StringInterpolation($expr);
		$expr->pos = $this->pos;

		return $expr;
	}

	private function read_dollar_identifier(): Identifiable
	{
		// $xxx style

		$token = $this->get_token();
		if (!TeaHelper::is_identifier_name($token)) {
			throw $this->new_unexpected_error();
		}

		$this->scan_token();

		$next = $this->get_token();
		if ($next === _DOT) {
			$temp_pos = $this->pos;
			$this->scan_token(); // temp skip the dot

			$next = $this->get_token();
			if (TeaHelper::is_identifier_name($next)) {
				$this->scan_token();
				throw $this->new_parse_error('The member accessing interpolations required \'${}\'.');
			}

			$this->pos = $temp_pos;
		}
		elseif ($next === _BRACKET_OPEN) {
			$this->scan_token();
			throw $this->new_parse_error('The key accessing interpolations required \'${}\'.');
		}

		$identifer = $this->factory->create_identifier($token);
		$identifer->pos = $this->pos;

		return $identifer;
	}

	protected static function collect_and_reset_temp(array &$items, string &$string, BaseExpression $expression)
	{
		if ($string !== '') {
			$items[] = $string;
			$string = ''; // reset
		}

		$items[] = $expression;
	}
}

// end
