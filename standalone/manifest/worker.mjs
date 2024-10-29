import { readFile, stat } from 'node:fs/promises';
import { relative } from 'node:path';
import { createHash } from 'node:crypto';
import { parentPort, workerData } from 'node:worker_threads';

const MAX_CONCURRENT_FILES = 4;

class FileQueue {
  #limit;
  #current = 0;
  #queue = [];

  constructor(limit) {
    this.#limit = limit;
  }

  enqueue = task =>
    new Promise((resolve, reject) => {
      this.#queue.push({ task, resolve, reject });
      this.#dequeue();
    });

  #dequeue = () => {
    if (this.#current >= this.#limit || !this.#queue.length)
      return;
    const { task, resolve, reject } = this.#queue.shift();
    this.#current++;
    task().then(resolve, reject).finally(() => {
      this.#current--;
      this.#dequeue();
    });
  };
}

const fileQueue = new FileQueue(MAX_CONCURRENT_FILES);

const processFiles = async (files, startingDirectory) =>
  Promise.all(files.map(async file => {
    const url = relative(startingDirectory, file);
    const [ size, md5 ] = await Promise.all([
      fileQueue.enqueue(() => stat(file).then(({ size }) => size)),
      fileQueue.enqueue(() => readFile(file).then(data => createHash('md5').update(data).digest('hex')))
    ]);
    return { file_list: [{ size, url }], md5, url };
  }));

processFiles(workerData.files, workerData.startingDirectory).then(result => parentPort.postMessage(result));
