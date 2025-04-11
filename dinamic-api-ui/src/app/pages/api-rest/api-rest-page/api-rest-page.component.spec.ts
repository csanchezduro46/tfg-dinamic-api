import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ApiRestPageComponent } from './api-rest-page.component';

describe('ApiRestPageComponent', () => {
  let component: ApiRestPageComponent;
  let fixture: ComponentFixture<ApiRestPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ApiRestPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ApiRestPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
