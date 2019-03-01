const API_URL = 'http://localhost:8000/nodes';

  window.onload = function () {
    // Getting nodes from API
    getNodes().then(response => {
      const data = response.data[0];
      createNode(data, 'root');

      /**
       * Adding event on add button click and calling create API
       */
      const addButtons = document.querySelectorAll('.addNode');
      addButtons.forEach(function(elem) {
        elem.addEventListener('click', function (event) {
          const newNodeName = prompt('Type name:');
          if (newNodeName) {
            const parentId = parseInt(event.target.getAttribute('parent-id'));
            const isLeft = event.target.getAttribute('is-left') === "1" ? true : false;

            addNode(newNodeName, parentId, isLeft).then(response => {
              location.reload();
            }, error => {
              console.error(error);
            });
          } else {
            alert('Name is mandatory!');
          }
        });
      });

      /**
       * Adding event to delete buttons and calling delete API.
       */
      const deleteButtons = document.querySelectorAll('.delete');
      deleteButtons.forEach(function(elem) {
        elem.addEventListener('click', function(event) {
          const nodeId = event.target.getAttribute('node-id');

          deleteNode(nodeId).then(response => {
            location.reload();
          }, error => {
            console.error(error);
          });
        });
      });

    }, error => {
      console.error(error);
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
function createNode(currentNode, parentContainerId) {
  const newChildNode = document.createElement('li');
  const children = currentNode.children;
  let isLeafNode = false;
  if (children.length === 0) {
    isLeafNode = true;
  }
  const html = createNodeHTML(
    currentNode.id,
    currentNode.name,
    currentNode.creditsLeft,
    currentNode.creditsRight,
    isLeafNode
  );
  newChildNode.innerHTML = html;
  document.getElementById(parentContainerId).appendChild(newChildNode);

  const leftChild = children.find(child => child.isLeft === true) || null;
  const rightChild = children.find(child => child.isLeft === false) || null;
  createChild(leftChild, currentNode, true);
  createChild(rightChild, currentNode, false);
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
    <ul id="${name}"></ul>
  `;
}

/**
 * Calls recursively createNode if there is child or add option for adding
 * child if parent does not have left/right child.
 *
 * @param child {object}
 * @param parent {object}
 * @param isLeft {boolean}
 */
function createChild(child, parent, isLeft) {
  if (child) {
    createNode(child, parent.name);
  } else {
    createAddNode(parent, isLeft);
  }
}

/**
 * Creating node with add button.
 * @param parent {object}
 * @param isLeft {boolean}
 */
function createAddNode(parent, isLeft) {
  const newChildNode = document.createElement('li');
  const html = `
    <div>
      <button class="addNode" parent-id="${parent.id}" is-left="${isLeft ? 1 : 0}">
        Add node
      </button>
    </div>
  `;
  newChildNode.innerHTML = html;
  document.getElementById(parent.name).appendChild(newChildNode);

}

// API CRUD CALLS

function addNode(name, parentId, isLeft) {
  return new Promise((resolve, reject) => {
    const data = {name, parentId, isLeft, creditsLeft: 0, creditsRight: 0}
    axios.post(`${API_URL}/add`, data)
      .then(function (response) {
        console.log(response);
        resolve(response);
      })
      .catch(function (error) {
        reject(error);
      });
  });
}

function getNodes() {
  return new Promise((resolve, reject) => {
    axios.get(`${API_URL}`)
      .then(function (response) {
        resolve(response);
      })
      .catch(function (error) {
        reject(error);
      });
  })

}

function deleteNode(id) {
  return new Promise((resolve, reject) => {
    axios.delete(`${API_URL}/${id}`)
      .then(function (response) {
        resolve(response);
      })
      .catch(function (error) {
        reject(error);
      });
  });
}