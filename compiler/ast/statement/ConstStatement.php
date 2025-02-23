<?php
/**
 * This file is part of the Tea programming language project
 * @copyright 	(c)2019 tealang.org
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Tea;

class ConstStatement extends BaseStatement
{
	const KIND = 'const_statement';

	/**
	 * @var ConstantDeclaration[]
	 */
	public $members;

	public function __construct(array $members)
	{
		$this->members = $members;
	}
}

// end
