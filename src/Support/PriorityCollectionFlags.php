<?php
declare(strict_types=1);

namespace Smpl\Collections\Support;

final class PriorityCollectionFlags
{

    /**
     * Prioritise the collection with null values first.
     */
    public final const NULL_VALUE_FIRST = 4;
    /**
     * Do not allow null values.
     */
    public final const NO_NULL = 16;
    /**
     * Prioritise the collection in descending order.
     */
    public final const DESC_ORDER = 2;
    /**
     * Put elements with no priority first.
     */
    public final const NO_PRIORITY_FIRST = 32;
    /**
     * Put elements with no priority last.
     */
    public final const NO_PRIORITY_LAST = 64;
    /**
     * Prioritise the collection in ascending order.
     */
    public final const ASC_ORDER = 1;
    /**
     * Prioritise the collection with null values last.
     */
    public final const NULL_VALUE_LAST = 8;
}