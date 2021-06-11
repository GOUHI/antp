import request from '../request';

export default{
  index(data){
    return request.post('/index/login',data)
  }
}