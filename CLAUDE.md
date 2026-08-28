# CLAUDE.md

This file provides guidance to Claude Code when working with code in this repository.

## Guidelines

All development guidelines for this project are defined in [AGENTS.md](AGENTS.md). **Read that file before making any change** — it is the single source of truth for:

- Project overview, directory layout and requirements
- Bootstrapping and the automatic class instantiation flow (`Core\Init`)
- PHP coding style, docblock and `@since` / `@version` conventions
- Hook naming (`Woo_Custom_Installments/...`) and the legacy deprecation bridge
- Settings storage, defaults and Pro (license) gating
- Views, templates, components and asset handling (including `.min` files)
- Internationalization, integrations, license and update endpoints
- Release process, Git workflow, manual testing and boundaries

The changelog lives in [CHANGELOG.md](CHANGELOG.md), in English, following [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). Record every change under `## [Unreleased]` as you make it.

## Language

Write all code, comments, docblocks, commit messages and documentation in **English (en_US)**. The only exception is user-facing strings passed through translation functions, which stay in Brazilian Portuguese (pt_BR) to match the existing catalog.
