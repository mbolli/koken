<?php
/**
 *
 * @author A. Grandt <php@grandt.com>
 *
 * Classes to assist in handling extra fields
 *
 */

namespace ZipMerge\Zip\Core\ExtraField;

use com\grandt\BinStringStatic;

/**
 *
 *            -Info-ZIP Unicode Comment Extra Field:
 *            ====================================
 *
 *            Stores the UTF-8 version of the entry comment as stored in the
 *            central directory header.
 *            (Last Revision 20070912)
 *
 *            Value         Size        Description
 *            -----         ----        -----------
 *    (UCom)  0x6375        Short       tag for this extra block type ("uc")
 *            TSize         Short       total data size for this block
 *            Version       1 byte      version of this extra field, currently 1
 *            ComCRC32      4 bytes     Comment Field CRC32 Checksum
 *            UnicodeCom    Variable    UTF-8 version of the entry comment
 *
 *            Currently Version is set to the number 1.  If there is a need
 *            to change this field, the version will be incremented.  Changes
 *            may not be backward compatible so this extra field should not be
 *            used if the version is not recognized.
 *
 *            The ComCRC32 is the standard zip CRC32 checksum of the Comment
 *            field in the central directory header.  This is used to verify that
 *            the comment field has not changed since the Unicode Comment extra
 *            field was created.  This can happen if a utility changes the Comment
 *            field but does not update the UTF-8 Comment extra field.  If the CRC
 *            check fails, this Unicode Comment extra field should be ignored and
 *            the Comment field in the header used.
 *
 *            The UnicodeCom field is the UTF-8 version of the entry comment field
 *            in the header.  As UnicodeCom is defined to be UTF-8, no UTF-8 byte
 *            order mark (BOM) is used.  The length of this field is determined by
 *            subtracting the size of the previous fields from TSize.  If both the
 *            File Name and Comment fields are UTF-8, the new General Purpose Bit
 *            Flag, bit 11 (Language encoding flag (EFS)), can be used to indicate
 *            both the header File Name and Comment fields are UTF-8 and, in this
 *            case, the Unicode Path and Unicode Comment extra fields are not
 *            needed and should not be created.  Note that, for backward
 *            compatibility, bit 11 should only be used if the native character set
 *            of the paths and comments being zipped up are already in UTF-8.  The
 *            same method, either bit 11 or extra fields, should be used in both
 *            the local and central directory headers.
 */
class UnicodeCommentExtraField extends AbstractUnicodeExtraField
{
    public function __construct($handle = null)
    {
        parent::__construct($handle);
        if ($handle == null) {
            $this->header = parent::HEADER_UNICODE_COMMENT;
        }
    }

    /**
     * @return string The version of the field for the Local Header.
     */
    #[\Override]
    public function getLocalField()
    {
        return ''; // Comments are not added to the Local header.
    }

    /**
     * @return string The version of the field for the Central Header.
     */
    #[\Override]
    public function getCentralField()
    {
        return parent::HEADER_UNICODE_COMMENT . pack('vV', BinStringStatic::_strlen($this->utf8Data) + 5, $this->CRC32) .  $this->version . $this->utf8Data;
    }
}
