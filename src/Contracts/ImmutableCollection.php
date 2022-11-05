<?php

namespace Smpl\Collections\Contracts;

/**
 * Immutable Collection Contract
 *
 * This contract exists as a marker interface to mark a particular collection
 * as being immutable. This is useful for the handling of collections, allowing
 * you to avoid {@see \Smpl\Collections\Exceptions\UnsupportedOperationException}
 * exceptions.
 */
interface ImmutableCollection
{

}