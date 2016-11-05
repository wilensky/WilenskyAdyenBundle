<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Traits;

/**
 * Adyen merchant signature calculator
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
trait MerchantSignatureTrait
{
    /**
     * HMAC with auto-generated key
     * @param string $hmac_key
     * @param array $data
     * @return string
     */
    protected function SHA256signature(string $hmac_key, array $data): string
    {
        ksort($data, SORT_STRING); // Sort the array by key using SORT_STRING order

        $signData = implode(":", array_map(// Generate the signing data string
            // `$v` can be `string` or `null`
            function ($v) {
                return str_replace(':', '\\:', str_replace('\\', '\\\\', $v));
            }, array_merge(array_keys($data), array_values($data))
        ));  // ) `array_map` ) `implode`

        return base64_encode(hash_hmac('sha256', $signData, pack('H*', $hmac_key), true));
    }

    /**
     * HMAC with user-defined key
     * @link https://docs.adyen.com/manuals/hpp-manual#hmacpaymentsetupsha1deprecated
     * @see SHA256signature()
     * @deprecated since `SHA256signature()`
     * @param string $hmac_key
     * @param array $data
     * @return string
     */
    private function SHA1signature(string $hmac_key, array $data): string
    {
        return base64_encode(
            pack(
                'H*',
                hash_hmac(
                    'sha1',
                    array_reduce(
                        $data,
                        function ($carry, $f) {
                            $carry .= $this->getData($f);
                            return $carry;
                        },
                        ''
                    ), // array_reduce
                    $hmac_key
                ) // hash_hmac
            ) // pack
        ); // base64_encode
    }
}
