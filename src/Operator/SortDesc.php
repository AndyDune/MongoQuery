<?php
/**
 * ----------------------------------------------
 * | Author: Андрей Рыжов (Dune) <info@rznw.ru>  |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 09.11.2017                               |
 * -----------------------------------------------
 *
 */


namespace AndyDune\MongoQuery\Operator;


class SortDesc extends SortAsc
{
    protected $sortOrder = -1;
}