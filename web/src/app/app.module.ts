import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { HttpModule, RequestOptions } from '@angular/http';

import { ApplicationRef, NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule } from '@angular/router';
/*
 * Platform and Environment providers/directives/pipes
 */
import { ENV_PROVIDERS } from './environment';
import { ROUTES } from './app.routes';
// App is our top level component
import { AppComponent } from './app.component';
import { APP_RESOLVER_PROVIDERS } from './app.resolver';
import { NoContentComponent } from './no-content';
import { ApiOptions } from "./app.api-options";

import '../styles/styles.scss';
import '../styles/headings.css';
import { LoginModule } from './login';

const APP_PROVIDERS = [
  ...APP_RESOLVER_PROVIDERS,
  {
    provide: RequestOptions, useClass: ApiOptions
  }
];

@NgModule({
  bootstrap: [AppComponent],
  declarations: [
    AppComponent,
    NoContentComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpModule,
    LoginModule,
    RouterModule.forRoot(ROUTES, {useHash: true, preloadingStrategy: PreloadAllModules}),
  ],
  providers: [
    ENV_PROVIDERS,
    APP_PROVIDERS
  ]
})
export class AppModule {
  constructor(public appRef: ApplicationRef) {}
}
