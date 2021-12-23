<?php

/**
 *  Copyright (c) 2021 cooldogedev
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
 */

declare(strict_types=1);

namespace cooldogedev\EconAPIToBE\command;

use cooldogedev\BedrockEconomy\BedrockEconomy;
use cooldogedev\BedrockEconomy\constant\SearchConstants;
use cooldogedev\EconAPIToBE\EconAPIToBE;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class ConversionCommand extends Command
{

    public function __construct(protected EconAPIToBE $plugin)
    {
        parent::__construct("econapiconvert", "Convert all of your EconomyAPI Data to BedrockEconomy", null, ["eac"]);
        $this->setPermission("econapitobe.convert");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) {
            return;
        }
        $data = EconomyAPI::getInstance()->getAllMoney();

        foreach ($data as $username => $balance) {
            if (BedrockEconomy::getInstance()->getAccountManager()->hasAccount($username, SearchConstants::SEARCH_MODE_USERNAME)) {
                BedrockEconomy::getInstance()->getAccountManager()->getAccount($username, SearchConstants::SEARCH_MODE_USERNAME)->setBalance($balance);
            } else {
                BedrockEconomy::getInstance()->getAccountManager()->addAccount($username, $username, (int)floor($balance), true);
            }
            $this->getPlugin()->getLogger()->debug("Successfully converted " . $username . "'s balance (" . $balance . ")");
        }
    }

    public function getPlugin(): EconAPIToBE
    {
        return $this->plugin;
    }
}