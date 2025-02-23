<?php
/**
 * This file is part of the Tea programming language project
 * @copyright 	(c)2019 tealang.org
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Tea;

// for classs/functions/constants
class UseDeclaration extends Node implements IMemberDeclaration
{
	use DeclarationTrait;

	const KIND = 'use_declaration';

	public $program;

	public $ns;

	public $name;

	public $target_name;

	public $source_name;

	public $source_declaration;

	public function __construct(NamespaceIdentifier $ns, string $target_name = null, string $source_name = null)
	{
		$this->ns = $ns;
		$this->name = $target_name ?? $ns->get_last_name();
		$this->target_name = $target_name;
		$this->source_name = $source_name;
	}
}
