<?php declare(strict_types=1);

/**
 * This file is part of MadelineProto.
 * MadelineProto is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * MadelineProto is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU General Public License along with MadelineProto.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Daniil Gentili <daniil@daniil.it>
 * @copyright 2016-2023 Daniil Gentili <daniil@daniil.it>
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPLv3
 * @link https://docs.madelineproto.xyz MadelineProto documentation
 */

namespace danog\MadelineProto\EventHandler;

use danog\MadelineProto\EventHandler\ChatInvite\ChatInviteExported;
use danog\MadelineProto\EventHandler\ChatInvite\ChatInvitePublicJoin;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

/**
 * Chat invite link that was used by the user to send the [join request »](https://core.telegram.org/api/invites#join-requests).
 */
abstract class ChatInvite implements JsonSerializable
{
    public static function fromRawChatInvite(array $rawChatInvite): self
    {
        return match($rawChatInvite['_'])
        {
            'chatInviteExported' => new ChatInviteExported($rawChatInvite),
            'chatInvitePublicJoinRequests' => new ChatInvitePublicJoin($rawChatInvite),
            default => new \AssertionError('Unknown ChatInvite type \'_\':' . $rawChatInvite['_']),
        };
    }

    /** @internal */
    public function jsonSerialize(): mixed
    {
        $res = ['_' => static::class];
        $refl = new ReflectionClass($this);
        foreach ($refl->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $res[$prop->getName()] = $prop->getValue($this);
        }
        return $res;
    }
}
