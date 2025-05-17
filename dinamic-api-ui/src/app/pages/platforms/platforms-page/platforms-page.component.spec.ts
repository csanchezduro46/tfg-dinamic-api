import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PlatformsPageComponent } from './platforms-page.component';

describe('PlatformsPageComponent', () => {
  let component: PlatformsPageComponent;
  let fixture: ComponentFixture<PlatformsPageComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PlatformsPageComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PlatformsPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
