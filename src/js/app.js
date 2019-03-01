const API_URL = 'http://localhost:8000/nodes';
const NODE_LIMIT = 1000;
const NUMBER_OF_START_ROWS = 8;

window.onload = function () {
  getNumberOfRows().then(response => {
    const numberOfRows = response.data[0].lastRow;
    // Getting first 8 rows of nodes.
    getNodes(0, NUMBER_OF_START_ROWS, 0).then(response => {
      handleNodes(response.data, true);
      // Processing rest of the rows by 1000 nodes per request
      proceessRows(numberOfRows);
    });
  }, error => {
      console.error(error);
  });
}

/**
 * Iterates through rows to get nodes. If row has more
 * than 1000 elements it will request it in packages by 1000 for request.
 * @param numberOfRows
 * @returns {Promise<void>}
 */
async function proceessRows(numberOfRows) {
  const rowsRange = createRange(NUMBER_OF_START_ROWS + 1, numberOfRows);
  for (const currentRow of rowsRange) {
      const numberOfNodes = await getNumberOfNodes(currentRow).data[0].numberOfNodes;
      const numberOfPages = Math.ceil(numberOfNodes / NODE_LIMIT) - 1;
      await processNodes(currentRow, numberOfPages);
  }
}

/**
 * Iterates thought pages and requests for nodes.
 *
 * @param currentRow
 * @param numberOfPages
 * @returns {Promise<void>}
 */
async function processNodes(currentRow, numberOfPages) {
  const pageRange = createRange(0, numberOfPages);
  for (const currentPage of pageRange) {
    await getNodes(currentRow, currentRow, currentPage).then(response => {
      handleNodes(response.data);
    }, error => {
      console.error(error);
    });
  }
}

/**
 * Helper function for creating range of two given numbers.
 *
 * @param start
 * @param stop
 * @returns {Array}
 */
function createRange(start, stop) {
  let range = [];
  for(let i = start; i <= stop; i++) {
    range.push(i);
  }
  return range;
}

/**
 * If it is first time and there are several nodes in response
 * it sorts by row number first.
 * @param nodes
 * @param firstNodes
 */
function handleNodes(nodes, firstNodes = false) {
  let sortedNodes = nodes;
  if (firstNodes) {
    sortedNodes = nodes.sort((a, b) => a.depth - b.depth);
  }
  sortedNodes.forEach(node => {
    createNode(node);
  });
}

/**
 * Function creates HTML representation of node and adds it to
 * it's parent node list.Then recursively calls itself on child nodes.
 *
 * @param currentNode {object} Current node.
 * @param parentContainerId {string} Name of parent node,
 * also id of parent children list.
 */
function createNode(currentNode) {
  const newChildNode = document.createElement('li');

  const html = createNodeHTML(
    currentNode.id,
    currentNode.name,
    currentNode.creditsLeft,
    currentNode.creditsRight
  );
  newChildNode.innerHTML = html;
  const parent = document.getElementById(`list-${currentNode.parentId}`);
  if (currentNode.isLeft) {
    parent.prepend(newChildNode);
  } else {
    parent.appendChild(newChildNode);
  }

}

/**
 * Creates HTML for current node.
 * @param id {number}
 * @param name {string}
 * @param leftNumber {number}
 * @param rightNumber {number}
 * @param isLeafNode {boolean}
 * @returns {string}
 */
function createNodeHTML(id, name, leftNumber, rightNumber, isLeafNode = false) {
  return `
    <div>${leftNumber} | ${name} | ${rightNumber}</div>
    ${isLeafNode ? '<button class="delete" node-id="' + id +  '">Delete</button>' : ''}
    <ul id="list-${id}"></ul>
  `;
}

//// API CALLS ///

function getNodes(startingRow, endingRow = null, page) {
  return new Promise((resolve, reject) => {
    axios.get(`${API_URL}?startingRow=${startingRow}&endingRow=${endingRow}&page=${page}`)
        .then(function (response) {
          resolve(response);
        })
        .catch(function (error) {
          reject(error);
        });
  })

}

function getNumberOfRows() {
  return new Promise((resolve, reject) => {
    axios.get(`${API_URL}/numberOfRows`)
        .then(function (response) {
          resolve(response);
        })
        .catch(function (error) {
          reject(error);
        });
  });
}

function getNumberOfNodes(row) {
  return new Promise((resolve, reject) => {
    axios.get(`${API_URL}/numberOfNodes?row=${row}`)
        .then(function (response) {
          resolve(response);
        })
        .catch(function (error) {
          reject(error);
        });
  });
}