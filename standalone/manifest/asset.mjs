import { readdir } from 'node:fs/promises';
import { resolve, extname } from 'node:path';
import { Worker } from 'node:worker_threads';
import { cpus } from 'node:os';

const { length } = cpus();

const collectFiles = async (dir, ext) =>
  readdir(dir, { withFileTypes: true, recursive: true }).then(files => files
    .map(({ parentPath, name }) => resolve(parentPath, name))
    .filter(filePath => !ext || ext.includes(extname(filePath)))
  );

const worker = new URL('./worker.mjs', import.meta.url);

const generateFileListAndJSON = async (startingDirectory, files) =>
  Promise.all(Array.from({ length }, (_, i) =>
    new Promise((message, error) =>
      new Worker(worker, { workerData: { files: files.filter((_, idx) => idx % length === i), startingDirectory } })
        .on('message', message)
        .on('error', error)
        .on('exit', code => code !== 0 && error(new Error(`Worker stopped with exit code ${code}`)))
    )
  )).then($ => $.flat().sort((a, { file_list: [{ size }] }) => a.file_list[0].size - size));

const [,, startingDirectory, pathsFile, pretty ] = process.argv;
if (!startingDirectory) {
  console.error('Please provide a resource directory and paths file.');
  process.exit(1);
}
if (!pathsFile) {
  console.error('Please provide a paths file.');
  process.exit(1);
}

const files = await import(new URL(pathsFile, import.meta.url), { with: { type: 'json' } })
  .then(({ default: $ }) => Promise.all($.map(({ dir, ext }) => collectFiles(resolve(startingDirectory, dir), ext))))
  .then($ => $.flat());

console.warn(`Processing ${files.length} files in ${length} jobs...`);

const asset = await generateFileListAndJSON(startingDirectory, files);
console.log(pretty
  ? JSON.stringify(asset, null, 1).replace(/\[\s*([^,\[\]]+,\s*)?[^,\[\]]+\s*\]/g, m => m.replace(/\s+/g, ' '))
  : JSON.stringify(asset)
);
