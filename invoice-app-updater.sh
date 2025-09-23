#!/bin/bash
ROOT=${PWD};
INVOICE_PATH="./invoice"
# BRANCH="${1:-main}"
BRANCH="${1:-composition}"

if [ -d "$INVOICE_PATH" ]; then
    cd $INVOICE_PATH
    git checkout $BRANCH && git pull
else
    git clone git@github.com:Apirone/invoice.git $INVOICE_PATH
    cd $INVOICE_PATH
    git checkout $BRANCH
fi

cd ./app
rm -rf ./node_modules

yarn && yarn build
cd $ROOT

rm -rf ./src/assets && mkdir ./src/assets
cp -r ./$INVOICE_PATH/app/dist/* $ROOT/src/assets
rm -fr $INVOICE_PATH
