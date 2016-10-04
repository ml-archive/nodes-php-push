<?php

declare (strict_types = 1);

namespace Nodes\Push\Constants;


/**
 * Class AndroidSettings
 */
final class AndroidSettings
{
    /*
     *  VISIBILITY
     */

    /** Sends push normally. */
    const VISIBILITY_PUBLIC = 1;

    /** Shows a redacted version of the notification. */
    const VISIBILITY_PRIVATE = 0;

    /** Does not show any notifications */
    const VISIBILITY_SECRET = -1;

    /*
     *  STYLE
     */

    const STYLE_BIG_PICTURE = 'big_picture';

    const STYLE_BIG_TEXT = 'big_text';

    const STYLE_INBOX = 'inbox';

}