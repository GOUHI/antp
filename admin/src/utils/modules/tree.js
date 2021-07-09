/**
 *  动态路由懒加载
 *  这里需要注意，地址加载必须使用拼接。这里是判断位置然后截取，再拼回去
 */ 
const loadView = (view) => {
  if(view.indexOf('components') > 0){
    view = view.substr(11,view.length);
    return (resolve) => require([`@/components${view}`], resolve)
  }else{
    view = view.substr(6,view.length)
    return (resolve) => require([`@/views${view}`], resolve)
  }
 
}

/**
 * 后端数据转树形数据
 * @param {*} data 
 * @param {*} tree 
 * @param {*} id 
 * @returns 
 */
function dataToTree(data,tree,id){
  data.forEach(item=>{
    if(item.parent == id){
      
      const child = {
        children:[]
      }

      // 判断path是否存在
      if(item.path) child.path = item.path

      // 判断name是否存在
      if(item.name) child.name = item.name

      // 判断是否隐藏
      if(item.hidden && item.parent === 0){
        child.hideInMenu = true
      }else if(item.hidden && item.parent !== 0){
        child.hideChildrenInMenu = true
      }

      // 判断component地址,如果都没有的话，就是跳转链接
      if(item.view && !item.skip){
        child.component = loadView(item.view)
      }else if(!item.view && !item.skip){
        child.component = {render:h=> h("router-view")}
      }else{
        child.redirect = item.skip
      }

      // 判断是否有meta中的值
      if(item.icon) child.meta = {icon:item.icon}
      if(item.title) child.meta = {...child.meta,title:item.title}
      
      // 赋值继续迭代
      dataToTree(data,child.children,item.id)
      if(child.children.length <= 0){
        delete child.children
      }

      tree.push(child)
    }
  })

  return tree;
}

export default {
  dataToTree
}