# Changelog

All notable changes to `Query Filter Builder` will be documented in this file

## 1.1.4 - 2022-03-14

- when `is` or `not` receives `null`, it should turn into `empty`/`notEmpty`

## 1.1.3 - 2022-03-14

- added filterArray to easily handle comma separated strings as per recommendation in the JSON:API specification

## 1.1.2 - 2022-03-02

- a few bugfixes
- added default sorting option

# 1.1.1 - 2022-02-28

- fixed bug where joins weren't allowed when using custom filters with Laravel.

## 1.1.0 - 2022-02-24

- added FormRequest functionality to ease development in Laravel
- added asc/desc sorting functionality

## 1.0.0 - 2022-02-22

- initial release
