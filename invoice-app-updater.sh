#!/bin/bash
ROOT=${PWD};
INVOICE_PATH="./invoice-app"
BRANCH="${1:-main}"

if [ -d "$INVOICE_PATH" ]; then
    cd $INVOICE_PATH
    git checkout $BRANCH && git pull
else
    git clone git@github.com:Apirone/invoice-app.git $INVOICE_PATH
    cd $INVOICE_PATH
    git checkout $BRANCH
fi

rm -rf ./node_modules

yarn && yarn build
cd $ROOT

rm -rf ./src/assets && mkdir ./src/assets
cp -r ./$INVOICE_PATH/dist/* $ROOT/src/assets
rm -fr $INVOICE_PATH
