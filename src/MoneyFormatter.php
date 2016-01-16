<?php

namespace Money;

/**
 * Formats Money objects.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
interface MoneyFormatter {

    /**
     * Formats a Money object as string.
     *
     * @param Money $money
     *
     * @return string
     */
    public function format(Money $money);
}