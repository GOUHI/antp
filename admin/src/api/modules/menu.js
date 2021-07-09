import request from '../request';

export default{
  // 获取树形结构的菜单数据
  getMneuTree(){
    return request.get('/menu/tree')
  }
}