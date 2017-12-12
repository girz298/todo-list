import {
  BaseRequestOptions, RequestOptions, RequestOptionsArgs,
  URLSearchParams
} from '@angular/http';

export class ApiOptions extends BaseRequestOptions {
  constructor() {
    super();

    this.params = new URLSearchParams();
    this.params.append('api_token', localStorage.getItem('token'));
  }

  merge(options?: RequestOptionsArgs): RequestOptions {
    let params = new URLSearchParams();
    params.append('api_token', localStorage.getItem('token'));

    options.params = params;
    return super.merge(options);
  }
}
