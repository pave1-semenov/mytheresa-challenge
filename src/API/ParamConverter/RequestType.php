<?php

namespace Mytheresa\Challenge\API\ParamConverter;

enum RequestType
{
    case QUERY;
    case BODY;
}