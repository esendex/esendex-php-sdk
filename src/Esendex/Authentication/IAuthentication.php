<?php
namespace Esendex\Authentication;

interface IAuthentication
{
    function accountReference();

    function getEncodedValue();
}