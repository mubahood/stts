/**
 * @typedef { import('../../core/core.controller').default } Chart
 * @typedef { import('../../core/core.scale').default } Scale
 * @typedef { import('../../elements/element.point').default } PointElement
 */

import {LineElement} from '../../elements';
import {isArray} from '../../helpers';
import {_pointsFromSegments} from './filler.segment';

/**
 * @param {PointElement[] | { x: number; y: number; }} boundary
 * @param {LineElement} line
 * @return {LineElement?}
 */
export function _createBoundaryLine(boundary, line) {
  let points = [];
  let _loop = false;

  if (isArray(boundary)) {
    _loop = true;
    // @ts-ignore
    points = boundary;
  } else {
    points = _pointsFromSegments(boundary, line);
  }

  return points.length ? new LineElement({
    points,
    options: {tension: 0},
    _loop,
    _fullLoop: _loop
  }) : null;
}
