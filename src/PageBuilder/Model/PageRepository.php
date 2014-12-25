<?php
namespace PageBuilder\Model;

use SynergyCommon\Model\NestedSetRepository;
use SynergyCommon\ModelTrait\LocalAwareNestedSetTrait;

class PageRepository extends NestedSetRepository {
	use LocalAwareNestedSetTrait;
}
