#!/bin/bash

echo "============================================================="
echo "= CREATE DATABASE                                            "

echo "CREATE DATABASE IF NOT EXISTS apirone;" | "${mysql[@]}"

echo "============================================================="
