#!/usr/bin/env node

/**
 * Parcelas Customizadas para WooCommerce build pipeline.
 *
 * Produces a production-ready plugin package:
 *   1. Install production PHP dependencies (vendor/ via Composer, --no-dev).
 *   2. Verify the artifacts that are maintained by hand (minified assets,
 *      compiled translations) and that every version source agrees.
 *   3. Stage only the runtime files into build/woo-custom-installments/.
 *   4. Zip the staging dir into dist/woo-custom-installments.zip and
 *      dist/versions/<version>/woo-custom-installments.zip.
 *
 * The Composer packages under `vendor/` are not committed — only the generated
 * autoloader is — so step 1 is what puts the MDS SDK in the zip and is not
 * optional on a clean checkout. This plugin has no
 * bundler — every asset has a hand-written `.min` counterpart and the
 * translations are compiled outside this pipeline — so {@see verifyArtifacts}
 * fails the build rather than shipping a package whose production assets or
 * translations silently never load.
 *
 * Usage:
 *   node scripts/build.mjs [flags]
 *
 * Flags:
 *   --skip-composer   Don't run composer install (reuse the existing vendor/).
 *   --skip-checks     Don't verify assets, translations and versions.
 *   --no-zip          Stage files but don't create the .zip files.
 *   --no-versioned    Write dist/woo-custom-installments.zip only, no dist/versions/ copy.
 *   --keep-staging    Leave build/ in place after zipping (useful to inspect it).
 */

import { spawnSync } from 'node:child_process';
import { createWriteStream, existsSync } from 'node:fs';
import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

import archiver from 'archiver';

const __dirname = path.dirname( fileURLToPath( import.meta.url ) );
const root = path.resolve( __dirname, '..' );
const slug = 'woo-custom-installments';

const stagingRoot = path.join( root, 'build' );
const stagingDir = path.join( stagingRoot, slug );
const distDir = path.join( root, 'dist' );

/* ------------------------------------------------------------------ flags */

const argv = process.argv.slice( 2 );
const hasFlag = ( name ) => argv.includes( name );

const opts = {
    skipComposer: hasFlag('--skip-composer'),
    skipChecks: hasFlag('--skip-checks'),
    zip: ! hasFlag('--no-zip'),
    versioned: ! hasFlag('--no-versioned'),
    keepStaging: hasFlag('--keep-staging'),
};

/* ---------------------------------------------------------------- helpers */

const log = ( msg ) => console.log( `\x1b[36m▶\x1b[0m ${msg}` );
const ok = ( msg ) => console.log( `\x1b[32m✓\x1b[0m ${msg}` );
const warn = ( msg ) => console.log( `\x1b[33m!\x1b[0m ${msg}` );

/**
 * Run a command, failing the build when it does.
 *
 * @param {string} command Executable.
 * @param {string[]} args Arguments.
 * @param {string} cwd Working directory.
 * @return {void}
 */
function run( command, args, cwd ) {
    const printable = `${command} ${args.join(' ')}`;
    log( `${printable}  (in ${path.relative( root, cwd ) || '.'})` );

    // shell:true lets Windows resolve composer.bat from PATH. The command goes
    // in as one string because passing an args array alongside it is deprecated.
    const result = spawnSync( printable, {
        cwd,
        stdio: 'inherit',
        shell: true,
    } );

    if ( result.status !== 0 ) {
        throw new Error( `Command failed (exit ${result.status}): ${printable}` );
    }
}

/**
 * Copy a directory into the staging tree.
 *
 * @param {string} relSource Path relative to the plugin root.
 * @param {string} relDest Path relative to the staging dir.
 * @param {Function} filter Optional fs.cp filter.
 * @return {Promise<void>}
 */
async function copyDir( relSource, relDest = relSource, filter ) {
    const source = path.join( root, relSource );
    const dest = path.join( stagingDir, relDest );

    if ( ! existsSync( source ) ) {
        return;
    }

    await fs.cp( source, dest, { recursive: true, filter } );
}

/**
 * Copy a single file into the staging tree.
 *
 * @param {string} relSource Path relative to the plugin root.
 * @param {string} relDest Path relative to the staging dir.
 * @return {Promise<void>}
 */
async function copyFile( relSource, relDest = relSource ) {
    const source = path.join( root, relSource );

    if ( ! existsSync( source ) ) {
        return;
    }

    const dest = path.join( stagingDir, relDest );
    await fs.mkdir( path.dirname( dest ), { recursive: true } );
    await fs.copyFile( source, dest );
}

/**
 * List every file under a directory, recursively.
 *
 * @param {string} dir Absolute directory path.
 * @return {Promise<string[]>} Absolute file paths.
 */
async function walk( dir ) {
    if ( ! existsSync( dir ) ) {
        return [];
    }

    const entries = await fs.readdir( dir, { withFileTypes: true } );
    const files = await Promise.all(
        entries.map( ( entry ) => {
            const full = path.join( dir, entry.name );

            return entry.isDirectory() ? walk( full ) : [ full ];
        } ),
    );

    return files.flat();
}

/**
 * Read the version from the plugin header, the value WordPress itself trusts.
 *
 * @return {Promise<string>}
 */
async function getPluginVersion() {
    const file = path.join( root, `${slug}.php` );
    const contents = await fs.readFile( file, 'utf8' );
    const match = contents.match( /^\s*\*\s*Version:\s*(.+)$/m );

    return match ? match[1].trim() : '0.0.0';
}

/**
 * Zip the staging directory, nesting it under the plugin slug.
 *
 * @param {string} sourceDir Directory to archive.
 * @param {string} outPath Destination .zip path.
 * @return {Promise<number>} Bytes written.
 */
function zipDirectory( sourceDir, outPath ) {
    return new Promise( ( resolve, reject ) => {
        const output = createWriteStream( outPath );
        const archive = archiver( 'zip', { zlib: { level: 9 } } );

        output.on( 'close', () => resolve( archive.pointer() ) );
        archive.on( 'warning', ( err ) => ( err.code === 'ENOENT' ? null : reject( err ) ) );
        archive.on( 'error', reject );

        archive.pipe( output );
        // WordPress expects the plugin folder at the archive root.
        archive.directory( sourceDir, slug );
        archive.finalize();
    } );
}

/* ------------------------------------------------------------- copy rules */

// Dev clutter that may sneak into otherwise-shipped directories. The two agent
// documents are listed because Composer packages ship their own copies, and a
// distributed plugin has no business carrying someone else's contributor docs.
const denyList = new Set( [
    'node_modules',
    '.git',
    '.gitignore',
    '.gitattributes',
    '.github',
    '.env',
    '.DS_Store',
    'Thumbs.db',
    'AGENTS.md',
    'CLAUDE.md',
] );

const baseFilter = ( src ) => ! denyList.has( path.basename( src ) );

// languages/: ship only the catalogs, never editor leftovers or dotfiles.
const languageExtensions = new Set( [ '.po', '.mo', '.pot', '.json', '.php' ] );
const languageFilter = ( src ) => {
    const name = path.basename( src );

    // Dotfiles report an empty extname, so they would slip through the
    // directory branch below and end up shipped.
    if ( denyList.has( name ) || name.startsWith('.') ) {
        return false;
    }

    // Always allow directory entries so their children get evaluated.
    if ( ! path.extname( name ) ) {
        return true;
    }

    return languageExtensions.has( path.extname( name ) );
};

/* ----------------------------------------------------------------- stages */

/**
 * Install the production PHP dependencies (the MDS SDK lives here).
 *
 * @return {void}
 */
function installPhpDependencies() {
    if ( opts.skipComposer ) {
        log('Skipping Composer install (--skip-composer).');

        return;
    }

    run(
        'composer',
        [ 'install', '--no-dev', '--optimize-autoloader', '--no-interaction', '--no-progress' ],
        root,
    );
    ok('Production PHP dependencies installed (vendor/).');
}

/**
 * Check that every source asset has an up-to-date minified counterpart.
 *
 * `Assets::$min` resolves to `.min` outside debug mode, so a source file
 * without its `.min` sibling enqueues a 404 on every production store. The
 * `.min` files are written by hand, which is also why a source newer than its
 * minified twin is worth a warning: it usually means the pair was edited and
 * only half of it was updated.
 *
 * @return {Promise<string[]>} Errors found.
 */
async function verifyMinifiedAssets() {
    const files = await walk( path.join( root, 'assets' ) );
    const errors = [];
    let stale = 0;

    for ( const file of files ) {
        const ext = path.extname( file );

        // Third-party bundles under assets/vendor/ ship as their author built them.
        if ( ! [ '.js', '.css' ].includes( ext ) || file.includes( `${path.sep}vendor${path.sep}` ) ) {
            continue;
        }

        if ( file.endsWith( `.min${ext}` ) ) {
            continue;
        }

        const minified = `${file.slice( 0, -ext.length )}.min${ext}`;

        if ( ! existsSync( minified ) ) {
            errors.push( `${path.relative( root, file )} has no ${path.basename( minified )}` );

            continue;
        }

        const [ source, target ] = await Promise.all( [ fs.stat( file ), fs.stat( minified ) ] );

        if ( source.mtimeMs > target.mtimeMs ) {
            warn( `${path.relative( root, minified )} is older than its source — regenerate it.` );
            stale += 1;
        }
    }

    if ( ! errors.length && ! stale ) {
        ok('Minified assets verified.');
    }

    return errors;
}

/**
 * Check that every catalog was compiled to the formats WordPress loads.
 *
 * @return {Promise<string[]>} Errors found.
 */
async function verifyTranslations() {
    const langDir = path.join( root, 'languages' );
    const files = await walk( langDir );
    const errors = [];

    if ( ! existsSync( path.join( langDir, `${slug}.pot` ) ) ) {
        errors.push( `languages/${slug}.pot is missing` );
    }

    for ( const file of files.filter( ( item ) => item.endsWith('.po') ) ) {
        const base = file.slice( 0, -3 );

        for ( const ext of [ '.mo', '.l10n.php' ] ) {
            if ( ! existsSync( `${base}${ext}` ) ) {
                errors.push( `${path.relative( root, file )} was not compiled to ${ext}` );
            }
        }
    }

    if ( ! errors.length ) {
        ok('Translation catalogs verified.');
    }

    return errors;
}

/**
 * Check that every file carrying the version agrees with the plugin header.
 *
 * The header and `$plugin_version` are what the update channel compares, so a
 * mismatch there is fatal. The rest of the release checklist only feeds
 * documentation and the update screen, so it is reported and does not block a
 * build made mid-development.
 *
 * @param {string} version Version read from the plugin header.
 * @return {Promise<string[]>} Errors found.
 */
async function verifyVersions( version ) {
    const errors = [];

    const pluginFile = await fs.readFile( path.join( root, `${slug}.php` ), 'utf8' );
    const declared = pluginFile.match( /\$plugin_version\s*=\s*'([^']+)'/ );

    if ( ! declared ) {
        errors.push( `${slug}.php does not declare $plugin_version` );
    } else if ( declared[1] !== version ) {
        errors.push( `${slug}.php: header says ${version}, $plugin_version says ${declared[1]}` );
    }

    const init = await fs.readFile( path.join( root, 'inc/Core/Init.php' ), 'utf8' );
    const initVersion = init.match( /@version\s+([0-9][^\s*]*)/ );

    if ( initVersion && initVersion[1] !== version ) {
        warn( `inc/Core/Init.php @version is ${initVersion[1]}, expected ${version}.` );
    }

    const checkerPath = path.join( distDir, 'update-checker.json' );

    if ( existsSync( checkerPath ) ) {
        const checker = JSON.parse( await fs.readFile( checkerPath, 'utf8' ) );

        if ( checker.version !== version ) {
            warn( `dist/update-checker.json is at ${checker.version}, expected ${version}.` );
        }
    }

    const changelog = await fs.readFile( path.join( root, 'CHANGELOG.md' ), 'utf8' );

    if ( ! changelog.includes( `## [${version}]` ) ) {
        warn( `CHANGELOG.md has no "## [${version}]" section — still under [Unreleased]?` );
    }

    if ( ! errors.length ) {
        ok( `Version sources verified (${version}).` );
    }

    return errors;
}

/**
 * Refuse to package a build that is missing a generated artifact.
 *
 * @param {string} version Version read from the plugin header.
 * @return {Promise<void>}
 */
async function verifyArtifacts( version ) {
    if ( ! existsSync( path.join( root, 'vendor/autoload.php' ) ) ) {
        throw new Error('vendor/autoload.php is missing — run the build without --skip-composer.');
    }

    if ( opts.skipChecks ) {
        log('Skipping asset, translation and version checks (--skip-checks).');

        return;
    }

    const errors = [
        ...( await verifyMinifiedAssets() ),
        ...( await verifyTranslations() ),
        ...( await verifyVersions( version ) ),
    ];

    if ( errors.length ) {
        throw new Error( `Build checks failed:\n  - ${errors.join('\n  - ')}` );
    }
}

/**
 * Copy the runtime files into build/woo-custom-installments/.
 *
 * @return {Promise<void>}
 */
async function stageFiles() {
    log('Staging runtime files...');

    await fs.rm( stagingRoot, { recursive: true, force: true } );
    await fs.mkdir( stagingDir, { recursive: true } );

    // Top-level files. AGENTS.md, CLAUDE.md, docs/, scripts/ and the Composer
    // and npm manifests are development documents and deliberately stay out of
    // the package.
    for ( const file of [ `${slug}.php`, 'README.md', 'license.md', 'CHANGELOG.md' ] ) {
        await copyFile( file );
    }

    // PHP source + the production autoloader with the MDS SDK inside it.
    await copyDir( 'inc', 'inc', baseFilter );
    await copyDir( 'vendor', 'vendor', baseFilter );

    // Static assets and the templates themes may override.
    await copyDir( 'assets', 'assets', baseFilter );
    await copyDir( 'templates', 'templates', baseFilter );

    // Translation catalogs only.
    await copyDir( 'languages', 'languages', languageFilter );

    ok( `Staged at ${path.relative( root, stagingDir )}.` );
}

/**
 * Archive the staged tree into dist/, keeping a copy under dist/versions/.
 *
 * @param {string} version Plugin version.
 * @return {Promise<void>}
 */
async function packageZip( version ) {
    if ( ! opts.zip ) {
        log('Skipping zip (--no-zip).');

        return;
    }

    await fs.mkdir( distDir, { recursive: true } );

    const zipPath = path.join( distDir, `${slug}.zip` );
    await fs.rm( zipPath, { force: true } );

    const bytes = await zipDirectory( stagingDir, zipPath );
    ok( `ZIP created: ${path.relative( root, zipPath )} (${( bytes / 1024 / 1024 ).toFixed( 2 )} MB)` );

    if ( ! opts.versioned ) {
        return;
    }

    // The release checklist keeps one archive per version next to the current one.
    const versionedPath = path.join( distDir, 'versions', version, `${slug}.zip` );
    await fs.mkdir( path.dirname( versionedPath ), { recursive: true } );
    await fs.copyFile( zipPath, versionedPath );
    ok( `Archived: ${path.relative( root, versionedPath )}` );
}

/* -------------------------------------------------------------------- main */

async function main() {
    const version = await getPluginVersion();
    console.log( `\n\x1b[1mBuilding ${slug} v${version}\x1b[0m\n` );

    installPhpDependencies();
    await verifyArtifacts( version );
    await stageFiles();
    await packageZip( version );

    if ( opts.keepStaging ) {
        log( `Staging kept at ${path.relative( root, stagingRoot )} (--keep-staging).` );
    } else {
        await fs.rm( stagingRoot, { recursive: true, force: true } );
    }

    console.log( '\n\x1b[32m\x1b[1mBuild complete.\x1b[0m\n' );
}

main().catch( ( err ) => {
    console.error( `\n\x1b[31m✗ Build failed:\x1b[0m ${err.message}\n` );
    process.exit( 1 );
} );
