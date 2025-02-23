<?php
/**
 * This file is part of the Tea programming language project
 * @copyright 	(c)2019 tealang.org
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Tea;

interface IConstantDeclaration extends IValuedDeclaration {}

trait ConstantDeclarationTrait
{
	use DeclarationTrait;

	public $modifier;

	public $value;

	public function __construct(?string $modifier, string $name, BaseType $type = null, BaseExpression $value = null)
	{
		if ($modifier === _PUBLIC) {
			$this->is_unit_level = true;
		}

		$this->is_static = true;
		$this->modifier = $modifier;
		$this->name = $name;
		$this->declared_type = $type;
		$this->value = $value;
	}
}

class ConstantDeclaration extends RootDeclaration implements IConstantDeclaration, IRootDeclaration, IStatement
{
	use ConstantDeclarationTrait;

	const KIND = 'constant_declaration';

	public $is_static;
}

class ClassConstantDeclaration extends Node implements IConstantDeclaration, IClassMemberDeclaration
{
	use ClassMemberDeclarationTrait, ConstantDeclarationTrait;

	const KIND = 'class_constant_declaration';
}

// end
