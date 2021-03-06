<?php
/**
 * This file is part of the Alyx Gray OATH token bundle.
 *
 * (c) Alyx Gray <opensource@alyxgray.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlyxGray\OathTokenBundle;

use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * Represents an OATH token
 */
class OathToken
{

    /**
     * The secret was too small
     *
     * @var integer
     */
    const ERROR_INSUFFICIENT_SECRET_SIZE = 1;

    /**
     * An invalid value was specified for the counter
     *
     * @var integer
     */
    const ERROR_INVALID_COUNTER_VALUE = 2;

    /**
     * An invalid HMAC was specified
     *
     * @var integer
     */
    const ERROR_INVALID_HMAC = 3;

    /**
     * The HOTP size was invalid
     *
     * @var integer
     */
    const ERROR_INVALID_HOTP_SIZE = 4;

    /**
     * The token mode was invalid
     *
     * @var integer
     */
    const ERROR_INVALID_MODE = 5;

    /**
     * Secret size in bits
     *
     * @var integer
     */
    const GENERATED_SECRET_SIZE = 160;

    /**
     * HMAC-SHA-1 (required by RFC 4226) produces a 160 bit value
     *
     * @var integer
     */
    const HMAC_SIZE = 20;

    /**
     * Default character length for one time password, chosen to be more secure than minimum length
     *
     * @var integer
     */
    const HOTP_DEFAULT_SIZE = 8;

    /**
     * Minimum character length for one time password, per RFC 4226
     *
     * @var integer
     */
    const HOTP_MINIMUM_SIZE = 6;

    /**
     * Maximum character length for one time password, largest size supported with a 31 dynamic binary code
     *
     * @var integer
     */
    const HOTP_MAXIMUM_SIZE = 9;

    /**
     * Minimum number of bits, per RFC 4226 standards
     *
     * @var integer
     */
    const SECRET_MINIMUM_BITS = 128;

    /**
     * Recommended number of bits, per RFC 4226 standards
     *
     * @var integer
     */
    const SECRET_RECOMMENDED_BITS = 160;

    /**
     * This token generates OTPs based on a counter
     *
     * @var integer
     */
    const TOKEN_MODE_EVENT = 1;

    /**
     * This token generates OTPs based on time
     *
     * @var integer
     */
    const TOKEN_MODE_TIME = 2;

    /**
     * How many OTPs should we process (in event mode) before we give up?
     * @var integer
     */
    const WINDOW_DEFAULT = 30;

    /**
     * Token counter
     *
     * @var integer
     */
    protected $counter = null;

    /**
     * Token mode (event or time)
     *
     * @var integer
     */
    protected $mode = null;

    /**
     * Optional manufacturer id for token
     * @var string
     */
    protected $serial = null;

    /**
     * Client/Server shared secret
     *
     * @var string
     */
    protected $sharedSecret = null;

    /**
     * One time password size
     *
     * @var integer
     */
    protected $hotpSize = self::HOTP_DEFAULT_SIZE;

    /**
     * Authentication Window
     * @var unknown
     */
    protected $window = self::WINDOW_DEFAULT;

    /**
     * Instantiate a new token
     *
     * @param string  $sharedSecret
     *                Token shared secret
     * @param integer $mode
     *                Determines whether the token is event- or time-based
     * @param integer $counter
     *                Counter (for event-based tokens)
     */
    public function __construct($sharedSecret = null, $mode = self::TOKEN_MODE_EVENT, $counter = 0)
    {
        $this->setSecret($sharedSecret)->setMode($mode);
    }

    /**
     *
     * @return string|null
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Sets the token mode
     *
     * @param integer $mode
     * @throws \InvalidArgumentException
     * @return \AlyxGray\OathTokenBundle\OathToken
     */
    public function setMode($mode)
    {
        // Ensure the mode is valid
        if ($mode != self::TOKEN_MODE_EVENT && $mode != self::TOKEN_MODE_TIME) {
            throw new \InvalidArgumentException('Invalid mode specified', self::ERROR_INVALID_MODE);
        }

        // Store the mode in the object
        $this->mode = $mode;

        // Permits method chaining
        return $this;
    }

    /**
     * Set token shared secret
     *
     * @param string  $sharedSecret
     *                Token shared secret
     * @param boolean $ignoreRecommendedLength
     *                If true, use the minimum required secret length, rather than the recommended length
     * @throws \InvalidArgumentException
     * @return \AlyxGray\OathTokenBundle\OathToken
     */
    public function setSecret($sharedSecret, $ignoreRecommendedLength = false)
    {
        // Ensure that the shared secret meets complexity requirements
        $minLength = $ignoreRecommendedLength ? OathToken::SECRET_MINIMUM_BITS / 8 : OathToken::SECRET_RECOMMENDED_BITS / 8;
        if (strlen($sharedSecret) < $minLength) {
            throw new \InvalidArgumentException(sprintf('Secret provided contains %d bits, minimum permitted is %d.', strlen($sharedSecret) * 8, $minLength * 8), self::ERROR_INSUFFICIENT_SECRET_SIZE);
        }

        // Store the shared secret in the object
        $this->sharedSecret = $sharedSecret;

        // Permits method chaining
        return $this;
    }

    /**
     *
     * @param string $serial
     * @return \AlyxGray\OathTokenBundle\OathToken
     */
    public function setSerial($serial)
    {
        // Store the token serial in the object
        $this->serial = $serial;

        // Permits method chaining
        return $this;
    }

    /**
     * Set the token counter
     *
     * @param integer $counter
     *            New counter value
     * @throws \InvalidArgumentException
     * @return \AlyxGray\OathTokenBundle\OathToken
     */
    public function setCounter($counter)
    {
        // Ensures the counter is valid
        if (! is_integer($counter) || $counter < 0) {
            throw new \InvalidArgumentException('Invalid counter value specified', self::ERROR_INVALID_COUNTER_VALUE);
        }

        // Stores counter in the object
        $this->counter = $counter;

        // Permits method chaining
        return $this;
    }

    /**
     * Set the one time password size
     *
     * @param integer $hotpSize
     *            Size of one time password
     * @throws \InvalidArgumentException
     * @return \AlyxGray\OathTokenBundle\OathToken
     */
    public function setHotpSize($hotpSize)
    {
        // Ensures the OTP size is valid
        if (! is_numeric($hotpSize) || ($hotpSize < self::HOTP_MINIMUM_SIZE) || ($hotpSize > self::HOTP_MAXIMUM_SIZE)) {
            throw new \InvalidArgumentException('Invalid one time password size', self::ERROR_INVALID_HOTP_SIZE);
        }

        // Stores the OTP in the object
        $this->hotpSize = $hotpSize;

        // Permits method chaining
        return $this;
    }

    /**
     * Calculates HMAC
     *
     * @param string $key
     *            Secret key
     * @param string $value
     *            Counter or time to use for HOTP calculation
     * @return string
     */
    public static function calculateHMAC($key, $value)
    {
        return hash_hmac('sha1', $value, $key, true);
    }

    /**
     * Truncate an HMAC to a HOTP of desired size
     *
     * @param string  $hmac
     *                Binary representation of the hmac
     * @param integer $hotpSize
     *                Size of one time password
     * @throws \InvalidArgumentException
     * @return string One time password
     */
    public static function truncateHMAC($hmac, $hotpSize)
    {
        // Verify the OTP Size is valid
        if (! is_numeric($hotpSize) || ($hotpSize < self::HOTP_MINIMUM_SIZE) || ($hotpSize > self::HOTP_MAXIMUM_SIZE)) {
            throw new \InvalidArgumentException('Invalid one time password size', self::ERROR_INVALID_HOTP_SIZE);
        }

        // Verify the HMAC is valid
        if (! is_string($hmac) || (strlen($hmac) != self::HMAC_SIZE)) {
            throw new \InvalidArgumentException('Invalid HMAC specified', self::ERROR_INVALID_HMAC);
        }

        // Calculates offset by taking the 'binary and' of the last character of the hmac
        $offset = ord($hmac[19]) & 0x0F;

        // Extracts 4 bytes from the HOTP starting at the offset
        $dynamicBinaryCode = unpack('N', substr($hmac, $offset, 4));
        $dynamicBinaryCode = $dynamicBinaryCode[1];

        // Truncates to 31 bits
        $dynamicBinaryCode = $dynamicBinaryCode & 0x7FFFFFFF;

        // Divide the DBC by 10 ^ [OTP size] and take the remainder
        // This will generate a HOTP of the desired size
        $hotp = $dynamicBinaryCode % pow(10, $hotpSize);

        // Formats HOTP to the desired size
        $format = '%0'.$hotpSize.'d';

        return sprintf($format, $hotp);
    }

    /**
     * Calculates one-time password
     *
     * @param string  $key
     *                Secret key
     * @param string  $value
     *                Counter or time to use for HOTP calculation
     * @param integer $hotpSize
     * @return string Calculated one-time password
     */
    public static function calculateHOTP($key, $value, $hotpSize)
    {
        $hmac = self::calculateHMAC($key, $value);
        return self::truncateHMAC($hmac, $hotpSize);
    }

    /**
     * Generates a new token secret
     *
     * @return string
     */
    public function generateSecret()
    {
        $generator = new SecureRandom();
        $newSecret = $generator->nextBytes(self::GENERATED_SECRET_SIZE / 8);

        return $newSecret;
    }

    /**
     * Verify the HOTP
     * @param unknown $hotp
     * @return boolean
     */
    public function verify ($hotp) {
        for ($i = 0; $i < $this->window; $i++) {
            $counterValue = self::packCounter ($this->counter + $i);
            if ($hotp == self::calculateHOTP($this->sharedSecret, $counterValue, $this->hotpSize)) {
                $this->setCounter ($this->counter + $i + 1);
                return TRUE;
            }
        }
        return FALSE;
    }

    public static function packCounter ($counter) {
        return pack ('NNC*', $counter>>32, $counter & 0xFFFFFFFF);
    }

    /**
     *
     * @return the unknown_type
     */
    public function getWindow()
    {
        return $this->window;
    }

    /**
     *
     * @param unknown_type $window
     */
    public function setWindow($window)
    {
        $this->window = $window;
        return $this;
    }

}
